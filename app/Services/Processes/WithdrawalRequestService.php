<?php

namespace App\Services\Processes;

use App\Models\Commodity;
use App\Models\ImportingRequest;
use App\Models\Warehouse;
use App\Models\WithdrawalRequest;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class WithdrawalRequestService extends BaseService
{
    public function checkInventory($data)
    {
        foreach ($data['commodity_id'] as $key => $value) {
            $commodity = Commodity::query()->findOrFail($value);
            $warehouse_ids = $data['warehouse_id'][$commodity->id];
            foreach ($warehouse_ids as $warehouse_id) {
                $warehouse = Warehouse::query()->findOrFail($warehouse_id);
                $amount = $data['amount'][$commodity->id][$warehouse->id];
                $unit = $data['unit'][$key];
                $commodity_amount = $this->calculateCommodityAmount($amount, $unit);
                $exits_amount = $commodity->warehouses->find($warehouse->id)->pivot->commodity_amount ?? 0;
                if ( $exits_amount < $commodity_amount) {
                    $data['success'] = false;
                    $data['error'] = ' مقدار انتخابی برای کالای ' . $commodity->title . ' به اندازه کافی در انبار ' . $warehouse->title . ' وجود ندارد ';
                    return $data;
                }
            }
        }
        return [
            'success' => true,
        ];
    }

    public function create($data, $file)
    {
        DB::transaction(function () use ($data, $file) {
            $number = $this->generateUniqueNumber(WithdrawalRequest::class, 'number');
            $request = WithdrawalRequest::query()->create([
                'customer_id' => $data['customer_id'],
                'status' => 'awaiting_approval',
                'number' => $number,
            ]);
            foreach ($data['commodity_id'] as $key => $value) {
                $commodity[$value] = [
                    'amount' => json_encode($data['amount'][$value]),
                    'unit' => $data['unit'][$key],
                    'price' => $data['price'][$key] ?? null,
                ];
            }
                $user = auth()->user();
                $request->commodities()->attach($commodity);
                if (isset($data['comment'])) {
                    $request->comments()->create([
                        'user_id' => $user->id,
                        'body' => $data['comment'],
                    ]);
                }
                if (!empty($file)) {
                    $this->uploadFile($file, 'withdrawal-request', $request);
                }
        });
    }
    public function checkInventoryApproval(WithdrawalRequest $request){
        foreach ($request->commodities as $commodity){
            foreach ($commodity->withdrawal_amount as $value){
                $exits_inventory = $value['warehouse']->commodities->find($commodity->id)->pivot->commodity_amount ?? 0;
                $amount=$this->calculateCommodityAmount($value['amount'],$value['unit']);
                if ($amount > $exits_inventory){
                    $data['success'] = false;
                    $data['error'] = ' مقدار انتخابی برای کالای ' . $commodity->title . ' به اندازه کافی در انبار ' . $value['warehouse']['title'] . 'وجود ندارد ';
                    return $data;
                }
            }
        }
        return [
            'success' => true,
        ];
    }
    public function approvalRequest(WithdrawalRequest $request){
        DB::transaction(function () use ($request) {
            foreach ($request->commodities as $commodity) {
                foreach ($commodity->withdrawal_amount as $value){
                    $amount=$this->calculateCommodityAmount($value['amount'],$value['unit']);
                    $exits_commodity = $value['warehouse']->commodities->find($commodity->id);
                    $new_amount = round($exits_commodity->pivot->commodity_amount - $amount,2);
                    $value['warehouse']->commodities()->updateExistingPivot($commodity->id, ['commodity_amount' => $new_amount], false);
                    $warehouses[$value['warehouse']->id] = $value['warehouse'];
                }
                $this->warningCommodity($commodity);
            }
            $request->update([
                'status' => 'approvaled',
            ]);
            $this->recalculateWarehousesEmptySpace($warehouses);
        });
        return true;
    }

    public function rejectRequest(WithdrawalRequest $request){
        return $request->update([
            'status'=>'rejected',
        ]);
    }
}
