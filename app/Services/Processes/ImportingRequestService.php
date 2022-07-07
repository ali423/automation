<?php

namespace App\Services\Processes;

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
            $commodity[$value] = [
                'warehouses_id' => $data['warehouse_id'][$key],
                'amount' => $data['amount'][$key],
                'unit' => $data['unit'][$key],
            ];
        }
        $number = $this->generateUniqueNumber(ImportingRequest::class,'number');
        $user = auth()->user();
        DB::transaction(function () use ($data, $commodity, $user, $file,$number) {
            $request = ImportingRequest::query()->create([
                'status' => 'awaiting_approval',
                'number'=>$number,
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
        });
        return true;
    }

    public function update($importing_request, $data, $file)
    {
        foreach ($data['commodity_id'] as $key => $value) {
            $commodity[$value] = [
                'warehouses_id' => $data['warehouse_id'][$key],
                'amount' => $data['amount'][$key],
                'unit' => $data['unit'][$key],
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
            foreach ($importing_request->commodities as $value) {
                $warehouse = Warehouse::query()->where('id', $value->pivot->warehouses_id)->firstOrFail();
                $commodity_amount=$this->calculateCommodityAmount($value->pivot->amount, $value->pivot->unit);
                $add_amounts[$warehouse->id][]=$commodity_amount;
                $warehouses[$warehouse->id] = $warehouse;
                if ($warehouse->commodities->contains($value->id)) {
                    $exits_commodity = $warehouse->commodities->find($value->id);
                    $new_amount = round($exits_commodity->pivot->commodity_amount + $commodity_amount,2);
                    $warehouse->commodities()->updateExistingPivot($value->id, ['commodity_amount' => $new_amount], false);
                } else {
                    $warehouse->commodities()->attach([
                        $value->id => [
                            'commodity_amount' => $this->calculateCommodityAmount($value->pivot->amount, $value->pivot->unit),
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

    public function checkImporting($importing_request)
    {
        foreach ($importing_request->commodities as $commodity) {
            $commodity['kg_amount'] = $this->calculateCommodityAmount($commodity->pivot->amount, $commodity->pivot->unit);
            $sort_warehouse[$commodity->pivot->warehouses_id][] = $commodity;
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
    public function rejectImporting($importing_request){
        return $importing_request->update([
           'status'=>'rejected',
        ]);
    }


}
