<?php

namespace App\Services\Processes;

use App\Models\Commodity;
use App\Models\WithdrawalRequest;
use App\Services\BaseService;
use App\Services\InventoryService;
use App\Services\CommodityUnitService;
use Illuminate\Support\Facades\DB;

class WithdrawalRequestService extends BaseService
{
    protected $inventoryService;
    protected $commodityUnitService;

    public function __construct(InventoryService $inventoryService, CommodityUnitService $commodityUnitService)
    {
        $this->inventoryService = $inventoryService;
        $this->commodityUnitService = $commodityUnitService;
    }

    public function create($data, $file)
    {
        $number = $this->generateUniqueNumber(WithdrawalRequest::class, 'number');
        
        return DB::transaction(function () use ($data, $file, $number) {
            $user = auth()->user();
            $request = WithdrawalRequest::query()->create([
                'customer_id' => $data['customer_id'],
                'status' => 'awaiting_approval',
                'number' => $number,
            ]);
            
            foreach ($data['commodity_id'] as $key => $value) {
                $commodity[$value] = [
                    'amount' => $data['amount'][$key],
                    'unit_id' => $data['unit'][$key],
                    'price' => $data['price'][$key] ?? null,
                ];
            }
            
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
            
            return $request;
        });
    }

    public function update($withdrawal_request, $data, $file)
    {
        foreach ($data['commodity_id'] as $key => $value) {
            $commodity[$value] = [
                'amount' => $data['amount'][$key],
                'unit_id' => $data['unit'][$key],
                'price' => $data['price'][$key] ?? null,
            ];
        }
        
        $user = auth()->user();
        
        DB::transaction(function () use ($data, $commodity, $user, $file, $withdrawal_request) {
            $withdrawal_request->commodities()->sync($commodity);
            
            $withdrawal_request->update([
                'customer_id' => $data['customer_id'],
            ]);
            
            if (isset($data['comment'])) {
                $withdrawal_request->comments()->create([
                    'user_id' => $user->id,
                    'body' => $data['comment'],
                ]);
            }
            
            if (!empty($file)) {
                $this->uploadFile($file, 'withdrawal-request', $withdrawal_request);
            }
        });
        
        return true;
    }

    public function delete($withdrawal_request)
    {
        DB::transaction(function () use ($withdrawal_request) {
            $withdrawal_request->commodities()->detach();
            $withdrawal_request->delete();
        });
        
        return true;
    }

    public function checkWithdrawal($withdrawal_request)
    {
        foreach ($withdrawal_request->commodities as $commodity) {
            // Convert amount to main unit using CommodityUnitService
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $commodity,
                $commodity->pivot->amount,
                $commodity->pivot->unit_id
            );
            
            // Check if we have enough stock in inventory
            $available_stock = $this->inventoryService->getStockLevel($commodity->id, $commodity->unit_id);
            
            if ($amountInMainUnit > $available_stock) {
                $data['success'] = false;
                $data['error'] = 'کالای ' . $commodity->title . ' به مقدار کافی در موجودی وجود ندارد';
                return $data;
            }
        }
        
        $data['success'] = true;
        return $data;
    }

    public function checkWithdrawalData($data)
    {
        foreach ($data['commodity_id'] as $key => $commodityId) {
            $commodity = Commodity::find($commodityId);
            $amount = $data['amount'][$key];
            $unitId = $data['unit'][$key];
            
            // Convert amount to main unit using CommodityUnitService
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $commodity,
                $amount,
                $unitId
            );
            
            // Check if we have enough stock in inventory
            $available_stock = $this->inventoryService->getStockLevel($commodity->id, $unitId);
            
            if ($amountInMainUnit > $available_stock) {
                $result['success'] = false;
                $result['error'] = 'کالای ' . $commodity->title . ' به مقدار کافی در موجودی وجود ندارد';
                return $result;
            }
        }
        
        $result['success'] = true;
        return $result;
    }

    public function approvalWithdrawal($withdrawal_request)
    {
        DB::transaction(function () use ($withdrawal_request) {
            foreach ($withdrawal_request->commodities as $commodity) {
                // Convert amount to main unit for inventory operations
                $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                    $commodity,
                    $commodity->pivot->amount,
                    $commodity->pivot->unit_id
                );
                
                // Remove stock from inventory using the main unit
                $this->inventoryService->removeStock(
                    $commodity->id,
                    $commodity->unit_id, // Use commodity's main unit
                    $amountInMainUnit
                );
                
                // Check if commodity is running low and trigger warning
                $this->warningCommodity($commodity);
            }
            
            $withdrawal_request->update([
                'status' => 'approvaled',
            ]);
        });
        
        return true;
    }

    public function rejectWithdrawal($withdrawal_request)
    {
        return $withdrawal_request->update([
            'status' => 'rejected',
        ]);
    }

    public function validationSecondLayer($data)
    {
        $commodities = $data['commodity_id'];
        $units = $data['unit'];
        $amounts = $data['amount'];
        
        $array_counts = [
            count($commodities),
            count($units),
            count($amounts),
        ];
        
        $array_keys = array_merge(array_keys($commodities), array_keys($units), array_keys($amounts));
        
        if (count(array_unique($array_counts)) != 1 || count(array_unique($array_keys)) != count($commodities)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'materials' => ['اطلاعات نوع ماده و مقدار آن باید متناظر باشند.'],
            ]);
        }
    }

    public function checkExpiredRequest($withdrawal_request)
    {
        if (\Carbon\Carbon::now()->diffInDays($withdrawal_request->created_at) > 7) {
            $withdrawal_request->update([
                'status' => 'expired',
            ]);
            $data['success'] = false;
            $data['error'] = 'درخواست منقضی شده است';
            return $data;
        }
        $data['success'] = true;
        return $data;
    }
}
