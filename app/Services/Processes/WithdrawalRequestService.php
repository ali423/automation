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
        foreach ($data['commodity_id']  as $key => $value) {
            $commodity = Commodity::query()->findOrFail($value);
            $warehouse_ids = $data['warehouse_id']['commodity_id'];
            foreach ($warehouse_ids as $warehouse_id) {
                $warehouse = Warehouse::query()->findOrFail($warehouse_id);
                $amount = $data['amount']['commodity_id']['warehouse_id'];
                $unit= $data['unit'][$key];
                $commodity_amount=$this->calculateCommodityAmount($amount,$unit);
                $exits_amount = $commodity->warehouses->find($warehouse->id)->pivot->commodity_amount ?? null;
                if ($exits_amount == null || $exits_amount < $commodity_amount) {
                    $data['success'] = false;
                    $data['error'] = 'مقدار انتخابی برای کالای ' . $commodity->title . 'به اندازه کافی در انبار ' . $warehouse->title . 'وجود ندارد ';
                }
            }
        }
        return [
            'success' => true,
        ];
    }

    public function create($data,$file)
    {
        DB::transaction(function () use ($data,$file) {
            $number = $this->generateUniqueNumber(ImportingRequest::class, 'number');
            $request = WithdrawalRequest::query()->create([
                'customer_id' => $data['customer_id'],
                'status' => 'awaiting_approval',
                'number' => $number,
            ]);
            foreach ($data['commodity_id'] as $key => $value) {
                $warehouse_ids = $data['warehouse_id']['commodity_id'];
                foreach ($warehouse_ids as $warehouse_id) {
                    $commodity[$value] = [
                        'warehouses_id' => $warehouse_id,
                        'amount' => $data['amount']['commodity_id']['warehouse_id'],
                        'unit' => $data['unit'][$key],
                        'price' => $data['price'][$key] ?? null,
                    ];
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
                }

            }
        });
    }
}
