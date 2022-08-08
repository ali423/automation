<?php

namespace App\Services\Processes;

use App\Models\Commodity;
use App\Models\ImportingRequest;
use App\Models\Warehouse;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportingRequestService extends BaseService
{
    public function __construct()
    {
        //
    }

    public function create($data, $file)
    {

        foreach ($data['commodity_id'] as $key => $value) {
            $exists_commodity=Commodity::query()->findOrFail($value);
            if ($exists_commodity->type == 'material' && empty($data['purchase_price'][$key])){
                $error_price = \Illuminate\Validation\ValidationException::withMessages([
                    'purchase_price.' . $key  => ['قیمت خرید فرآرده باید وارد شود.'],
                ]);
                throw $error_price;
            }
            $commodity[$value] = [
                'warehouses_id' => $data['warehouse_id'][$key],
                'amount' => $data['amount'][$key],
                'unit' => $data['unit'][$key],
                'purchase_price' => $data['purchase_price'][$key] ?? null,
            ];
        }
        $number = $this->generateUniqueNumber(ImportingRequest::class, 'number');
        $user = auth()->user();
       return DB::transaction(function () use ($data, $commodity, $user, $file, $number) {
            $request = ImportingRequest::query()->create([
                'status' => 'awaiting_approval',
                'number' => $number,
            ]);
            $request->commodities()->attach($commodity);
            if (isset($data['comment'])) {
                $request->comments()->create([
                    'user_id' => $user->id,
                    'body' => $data['comment'],
                ]);
            }
            if (!empty($file)) {
                $this->uploadFile($file, 'importing-commodity', $request);
            }
            return $request;
        });
    }

    public function update($importing_request, $data, $file)
    {
        foreach ($data['commodity_id'] as $key => $value) {
            $exists_commodity=Commodity::query()->findOrFail($value);
            if ($exists_commodity->type == 'material' && empty($data['purchase_price'][$key])){
                $error_price = \Illuminate\Validation\ValidationException::withMessages([
                    'purchase_price.' . $key  => ['قیمت خرید فرآرده باید وارد شود.'],
                ]);
                throw $error_price;
            }
            $commodity[$value] = [
                'warehouses_id' => $data['warehouse_id'][$key],
                'amount' => $data['amount'][$key],
                'unit' => $data['unit'][$key],
                'purchase_price' => $data['purchase_price'][$key] ?? null,
            ];
        }
        $user = auth()->user();
        DB::transaction(function () use ($data, $commodity, $user, $file, $importing_request) {
            $importing_request->commodities()->sync($commodity);
            if (isset($data['comment'])) {
                $importing_request->comments()->create([
                    'user_id' => $user->id,
                    'body' => $data['comment'],
                ]);
            }
            if (!empty($file)) {
                $this->uploadFile($file, 'importing-commodity', $importing_request);
            }
        });
        return true;
    }

    public function delete($importing_request)
    {
        DB::transaction(function () use ($importing_request) {
            $importing_request->commodities()->detach();
            $importing_request->delete();
        });
        return true;
    }

    public function approvalImporting($importing_request)
    {
        DB::transaction(function () use ($importing_request) {
            foreach ($importing_request->commodities as $selected_commodity) {
                $selected_commodity->update([
                    'purchase_price'=>$this->calculateCommodityPrice($selected_commodity->pivot->purchase_price,$selected_commodity->pivot->unit)
                ]);
                $warehouse = Warehouse::query()->where('id', $selected_commodity->pivot->warehouses_id)->firstOrFail();
                $commodity_amount = $this->calculateCommodityAmount($selected_commodity->pivot->amount, $selected_commodity->pivot->unit);
                if ($selected_commodity->type == 'product') {
                    $this->subtractingIngredients($selected_commodity, $commodity_amount);
                }
                $add_amounts[$warehouse->id][] = $commodity_amount;
                $warehouses[$warehouse->id] = $warehouse;
                if ($warehouse->commodities->contains($selected_commodity->id)) {
                    $exits_commodity = $warehouse->commodities->find($selected_commodity->id);
                    $new_amount = round($exits_commodity->pivot->commodity_amount + $commodity_amount, 2);
                    $average_purchase_price=$this->calculateAveragePurchasePrice($selected_commodity->type,$selected_commodity->pivot->purchase_price,$commodity_amount,$exits_commodity->pivot->commodity_amount,$exits_commodity->pivot->average_purchase_price,$selected_commodity->pivot->unit);
                    $warehouse->commodities()->updateExistingPivot($selected_commodity->id, ['commodity_amount' => $new_amount], false);
                    $warehouse->commodities()->updateExistingPivot($selected_commodity->id, ['average_purchase_price' => $average_purchase_price], false);
                } else {
                    $warehouse->commodities()->attach([
                        $selected_commodity->id => [
                            'commodity_amount' => $this->calculateCommodityAmount($selected_commodity->pivot->amount, $selected_commodity->pivot->unit),
                            'average_purchase_price'=>$this->calculatePrimaryPrice($selected_commodity->type,$selected_commodity->pivot->purchase_price,$selected_commodity->pivot->unit)
                        ],
                    ]);
                }
            }
            $importing_request->update([
                'status' => 'approvaled',
            ]);
            $this->recalculateWarehousesEmptySpace($warehouses);
        });
        return true;
    }

    public function calculatePrimaryPrice($type,$price,$unit){
        if ($type == 'product'){
            return null;
        }
            return $this->calculateCommodityPrice($price,$unit);
    }

    public function calculateAveragePurchasePrice($type,$price,$amount,$previous_amount,$previous_price,$unit){
       if ($type == 'product'){
           return null;
       }
       $new_price=$this->calculateCommodityPrice($price,$unit);
           return round((($amount*$new_price)+($previous_amount*$previous_price))/($amount+$previous_amount),2);
    }

    public function checkImporting($importing_request)
    {
        foreach ($importing_request->commodities as $commodity) {
            $commodity['kg_amount'] = $this->calculateCommodityAmount($commodity->pivot->amount, $commodity->pivot->unit);
            $sort_warehouse[$commodity->pivot->warehouses_id][] = $commodity;
            if ($commodity->type == 'product') {
                foreach ($commodity->materials as $material) {
                    $required_amount = round(($material->pivot->percentage / 100) * $commodity['kg_amount']);
                    $exits_material_amount = array_column(array_column($material->warehouses->toArray(), 'pivot'), 'commodity_amount');
                    $total_material_amount = array_sum($exits_material_amount);
                    if ($required_amount > $total_material_amount) {
                        $data['success'] = false;
                        $data['error'] = $material->title . ' که یک از مواد تشکیل دهنده ' . $commodity->title . ' است به مقدار کافی در انبار وجود ندارد ';
                        return $data;
                    }
                }
            }
            unset($commodity['kg_amount']);
        }
        foreach ($sort_warehouse as $key => $value) {
            $warehouse = Warehouse::query()->where('id', $key)->firstOrFail();
            if ($warehouse->status != 'active') {
                $data['success'] = false;
                $data['error'] = ' انبار ' . $warehouse->title . '  در وضعیت فعال قرار ندارد لطفا انبار دیگری را انتخاب کنید.  ';
                return $data;
            }
            $total_amount = array_sum(array_column($value, 'kg_amount'));
            if ($total_amount > $warehouse->empty_space) {
                $data['success'] = false;
                $data['error'] = ' انبار ' . $warehouse->title . ' گنجایش کالا های انتخابی را ندارد. ';
                return $data;
            }
        }
        $data['success'] = true;
        return $data;
    }

    public function checkImportingStore($data)
    {
        for ($i = 0; $i < count($data['commodity_id']); $i++) {
            $res[] = [
                'commodity_id' => $data['commodity_id'][$i],
                'warehouse_id' => $data['warehouse_id'][$i],
                'unit' => $data['unit'][$i],
                'amount' => $data['amount'][$i],
            ];
        }
        foreach ($res as $value) {
            $warehouses[$value['warehouse_id']][] = $value;
            $value['kg_amount'] = $this->calculateCommodityAmount($value['amount'], $value['unit']);
            $warehouses[$value['warehouse_id']][] = $value;
        }
        foreach ($warehouses as $key => $value) {
            $warehouse = Warehouse::query()->where('id', $key)->firstOrFail();
            if ($warehouse->status != 'active') {
                $data['success'] = false;
                $data['error'] = ' انبار ' . $warehouse->title . '  در وضعیت فعال قرار ندارد لطفا انبار دیگری را انتخاب کنید.  ';
                return $data;
            }
            $total_amount = array_sum(array_column($value, 'kg_amount'));
            if ($total_amount > $warehouse->empty_space) {
                $data['success'] = false;
                $data['error'] = ' انبار ' . $warehouse->title . ' گنجایش کالا های انتخابی را ندارد. ';
                return $data;
            }
        }
        $data['success'] = true;
        return $data;
    }

    public function rejectImporting($importing_request)
    {
        return $importing_request->update([
            'status' => 'rejected',
        ]);
    }

    public function subtractingIngredients($commodity, $commodity_amount)
    {
        foreach ($commodity->materials as $material) {
            $required_amount = round(($material->pivot->percentage / 100) * $commodity_amount);
            $material_warehouses = $material->warehouses()->orderBy('commodity_amount', 'DESC')->get();
            foreach ($material_warehouses as $material_warehouse) {
                if ($material_warehouse->pivot->commodity_amount >= $required_amount) {
                    $material_new_amount = $material_warehouse->pivot->commodity_amount - $required_amount;
                    $material_warehouse->commodities()->updateExistingPivot($material->id, ['commodity_amount' => $material_new_amount], false);
                    break;
                } else {
                    $material_warehouse->commodities()->updateExistingPivot($material->id, ['commodity_amount' => 0], false);
                    $required_amount = $required_amount - $material_warehouse->pivot->commodity_amount;
                }
            }
            $this->warningCommodity($material);
        }
    }

    public function validationSecondLayer($data){
        $commodities=$data['commodity_id'];
        $units=$data['unit'];
        $amounts=$data['amount'];
        $warehouses=$data['warehouse_id'];
        $array_counts=[
            count($commodities),
            count($units),
            count($amounts),
            count($warehouses),
        ];
        $array_keys=array_merge(array_keys($commodities),array_keys($units),array_keys($amounts),array_keys($warehouses));
        if (count(array_unique($array_counts)) != 1 || count(array_unique($array_keys)) !=   count($commodities) ){
            throw \Illuminate\Validation\ValidationException::withMessages([
                'materials' => ['اطلاعات نوع ماده و مقدار آن باید متناظر باشند.'],
            ]);
        }
    }


}
