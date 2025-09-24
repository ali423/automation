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

    /**
     * Create a new withdrawal request
     *
     * @param array $data
     * @param mixed $file
     * @return WithdrawalRequest
     */
    public function create($data, $file)
    {
        $commodity = [];
        foreach ($data['commodity_id'] as $key => $value) {
            $commodity[$value] = [
                'amount' => $data['amount'][$key],
                'unit_id' => $data['unit_id'][$key],
                'price' => $data['price'][$key] ?? null,
            ];
        }
        
        $number = $this->generateUniqueNumber(WithdrawalRequest::class, 'number');
        $user = auth()->user();
        
        $request = WithdrawalRequest::query()->create([
            'customer_id' => $data['customer_id'],
            'status' => 'awaiting_approval',
            'number' => $number,
            'driver_name' => $data['driver_name'] ?? null,
            'driver_phone' => $data['driver_phone'] ?? null,
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'plate_serial' => $data['plate_serial'] ?? null,
            'plate_number' => $data['plate_number'] ?? null,
        ]);
        
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
    }

    /**
     * Update an existing withdrawal request
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @param array $data
     * @param mixed $file
     * @return bool
     */
    public function update($withdrawalRequest, $data, $file)
    {
        $commodity = [];
        foreach ($data['commodity_id'] as $key => $value) {
            $commodity[$value] = [
                'amount' => $data['amount'][$key],
                'unit_id' => $data['unit_id'][$key],
                'price' => $data['price'][$key] ?? null,
            ];
        }
        
        $user = auth()->user();
        
        $withdrawalRequest->commodities()->sync($commodity);
        
        $withdrawalRequest->update([
            'customer_id' => $data['customer_id'],
        ]);
        
        if (isset($data['comment'])) {
            $withdrawalRequest->comments()->create([
                'user_id' => $user->id,
                'body' => $data['comment'],
            ]);
        }
        
        if (!empty($file)) {
            $this->uploadFile($file, 'withdrawal-request', $withdrawalRequest);
        }
        
        return true;
    }

    /**
     * Delete a withdrawal request
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @return bool
     */
    public function delete($withdrawalRequest)
    {
        $withdrawalRequest->commodities()->detach();
        $withdrawalRequest->delete();
        
        return true;
    }

    /**
     * Check if withdrawal is possible for existing request
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @return array
     */
    public function checkWithdrawal($withdrawalRequest)
    {
        foreach ($withdrawalRequest->commodities as $commodity) {
            // Convert requested amount to main unit for comparison
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $commodity,
                $commodity->pivot->amount,
                $commodity->pivot->unit_id
            );
            
            // Check if we have enough stock in inventory using the main unit
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

    /**
     * Check if withdrawal data is valid before creation
     *
     * @param array $data
     * @return array
     */
    public function checkWithdrawalData($data)
    {
        foreach ($data['commodity_id'] as $key => $commodityId) {
            $commodity = Commodity::find($commodityId);
            $amount = $data['amount'][$key];
            $unitId = $data['unit_id'][$key];
            
            // Convert requested amount to main unit for comparison
            $amountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $commodity,
                $amount,
                $unitId
            );
            
            // Check if we have enough stock in inventory using the main unit
            $available_stock = $this->inventoryService->getStockLevel($commodity->id, $commodity->unit_id);
            
            if ($amountInMainUnit > $available_stock) {
                $result['success'] = false;
                $result['error'] = 'کالای ' . $commodity->title . ' به مقدار کافی در موجودی وجود ندارد';
                return $result;
            }
        }
        
        $result['success'] = true;
        return $result;
    }

    /**
     * Approve a withdrawal request
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @return bool
     */
    public function approvalWithdrawal($withdrawalRequest)
    {
        foreach ($withdrawalRequest->commodities as $commodity) {
            // Convert requested amount to main unit for inventory removal
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
        }
        
        $withdrawalRequest->update([
            'status' => 'approvaled',
        ]);
        
        return true;
    }

    /**
     * Reject a withdrawal request
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @return bool
     */
    public function rejectWithdrawal($withdrawalRequest)
    {
        return $withdrawalRequest->update([
            'status' => 'rejected',
        ]);
    }

    /**
     * Validate the second layer of data
     *
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validationSecondLayer($data)
    {
        $commodities = $data['commodity_id'];
        $units = $data['unit_id'];
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

    /**
     * Check if request has expired
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @return array
     */
    public function checkExpiredRequest($withdrawalRequest)
    {
        if (\Carbon\Carbon::now()->diffInDays($withdrawalRequest->created_at) > 7) {
            $withdrawalRequest->update([
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
