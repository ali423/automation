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
            
            // Remove stock from inventory and create adjustment record
            $this->inventoryService->removeStockWithAdjustment(
                $commodity->id,
                $commodity->unit_id, // Use commodity's main unit
                $amountInMainUnit,
                'withdrawal_approval',
                $withdrawalRequest->number, // Use withdrawal number as reason
                $withdrawalRequest
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

    /**
     * Cancel a withdrawal request and restore inventory
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @param string|null $cancelReason
     * @return bool
     * @throws \Exception
     */
    public function cancelWithdrawal($withdrawalRequest, $cancelReason = null)
    {
        // Check if withdrawal can be cancelled (only approvaled or done status)
        if (!in_array($withdrawalRequest->status, ['approvaled', 'done', 'completed'])) {
            throw new \Exception('تنها درخواست‌های تایید شده یا تکمیل شده را می‌توان لغو کرد.');
        }

        return DB::transaction(function () use ($withdrawalRequest, $cancelReason) {
            // Get ALL adjustments for this withdrawal (both removals and additions)
            // We need to reverse everything: corrections, returns, approvals, etc.
            $adjustments = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)->get();

            // Reverse each adjustment by inverting its effect
            foreach ($adjustments as $adjustment) {
                // If adjustment was removal (negative), add stock back
                // If adjustment was addition (positive), remove stock back
                if ($adjustment->amount < 0) {
                    // Removal: reverse by adding stock back
                    $this->inventoryService->addStockWithAdjustment(
                        $adjustment->commodity_id,
                        $adjustment->unit_id,
                        abs($adjustment->amount),
                        'withdrawal_cancellation',
                        $cancelReason ?? 'لغو درخواست شماره ' . $withdrawalRequest->number,
                        $withdrawalRequest
                    );
                } else if ($adjustment->amount > 0) {
                    // Addition: reverse by removing stock back
                    $this->inventoryService->removeStockWithAdjustment(
                        $adjustment->commodity_id,
                        $adjustment->unit_id,
                        abs($adjustment->amount),
                        'withdrawal_cancellation',
                        $cancelReason ?? 'لغو درخواست شماره ' . $withdrawalRequest->number,
                        $withdrawalRequest
                    );
                }
            }

            // Update withdrawal status
            $withdrawalRequest->update([
                'status' => 'cancelled',
            ]);

            return true;
        });
    }

        /**     * Apply shipping discrepancy corrections before processing returns
     * Handles: items not shipped, items shipped but unrecorded, quantity mismatches
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @param array $corrections Format: [
     *     ['type' => 'add_back', 'commodity_id' => X, 'amount' => Y, 'unit_id' => Z, 'reason' => 'optional'],
     *     ['type' => 'deduct', 'commodity_id' => X, 'amount' => Y, 'unit_id' => Z, 'reason' => 'optional'],
     *     ['type' => 'qty_adjustment', 'commodity_id' => X, 'amount' => Y, 'unit_id' => Z, 'reason' => 'optional']
     * ]
     * @return void
     * @throws \Exception
     */
    public function applyShippingCorrections($withdrawalRequest, $corrections)
    {
        foreach ($corrections as $correction) {
            $commodity = \App\Models\Commodity::find($correction['commodity_id']);
            if (!$commodity) {
                throw new \Exception("کالا با شناسه {$correction['commodity_id']} یافت نشد.");
            }

            // Convert amount to main unit for consistency
            $correctionAmountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                $commodity,
                $correction['amount'],
                $correction['unit_id']
            );

            if ($correction['type'] === 'add_back') {
                // Product was recorded but NOT shipped - return it to inventory
                $this->inventoryService->addStockWithAdjustment(
                    $correction['commodity_id'],
                    $commodity->unit_id,
                    $correctionAmountInMainUnit,
                    'manual_adjustment',
                    ($correction['reason'] ?? '') ? '[تصحیح ارسال: برگشت] ' . $correction['reason'] : '[تصحیح ارسال: برگشت] کالای ثبت شده اما ارسال نشده',
                    $withdrawalRequest
                );
            }
            else if ($correction['type'] === 'deduct') {
                // Product WAS shipped but NOT recorded - deduct it retroactively
                $this->inventoryService->deductStockWithAdjustment(
                    $correction['commodity_id'],
                    $commodity->unit_id,
                    $correctionAmountInMainUnit,
                    'manual_adjustment',
                    ($correction['reason'] ?? '') ? '[تصحیح ارسال: کسر] ' . $correction['reason'] : '[تصحیح ارسال: کسر] کالای ارسال شده اما ثبت نشده',
                    $withdrawalRequest
                );
            }
            else if ($correction['type'] === 'qty_adjustment') {
                $direction = $correction['direction'] ?? 'decrease';

                if ($direction === 'increase') {
                    // Increase shipped quantity retroactively (remove more stock)
                    $this->inventoryService->deductStockWithAdjustment(
                        $correction['commodity_id'],
                        $commodity->unit_id,
                        $correctionAmountInMainUnit,
                        'manual_adjustment',
                        ($correction['reason'] ?? '') ? '[تصحیح ارسال: مقدار] افزایش - ' . $correction['reason'] : '[تصحیح ارسال: مقدار] افزایش مقدار ارسالی',
                        $withdrawalRequest
                    );
                } else {
                    // Decrease shipped quantity (add stock back)
                    $this->inventoryService->addStockWithAdjustment(
                        $correction['commodity_id'],
                        $commodity->unit_id,
                        $correctionAmountInMainUnit,
                        'manual_adjustment',
                        ($correction['reason'] ?? '') ? '[تصحیح ارسال: مقدار] کاهش - ' . $correction['reason'] : '[تصحیح ارسال: مقدار] کاهش مقدار ارسالی',
                        $withdrawalRequest
                    );
                }
            }
        }
    }

    /**     * Process a sales return (partial or full) for a withdrawal request
     *
     * @param WithdrawalRequest $withdrawalRequest
     * @param array $returnData Format: ['commodity_id' => ['amount' => X, 'unit_id' => Y, 'reason' => 'optional']]
     * @return bool
     * @throws \Exception
     */
    public function processSalesReturn($withdrawalRequest, $returnData)
    {
        // Check if withdrawal was approved (can't return from unapproved/cancelled requests)
        if (!in_array($withdrawalRequest->status, ['approvaled', 'done', 'completed'])) {
            throw new \Exception('امکان برگشت از فروش تنها برای درخواست‌های تایید شده وجود دارد.');
        }

        return DB::transaction(function () use ($withdrawalRequest, $returnData) {
            foreach ($returnData as $commodityId => $data) {
                $returnAmount = $data['amount'];
                $unitId = $data['unit_id'];
                $reason = $data['reason'] ?? null;

                // Get the commodity for conversion
                $commodity = \App\Models\Commodity::find($commodityId);
                if (!$commodity) {
                    throw new \Exception("کالا با شناسه {$commodityId} یافت نشد.");
                }

                // Convert return amount to main unit for processing
                $returnAmountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                    $commodity,
                    $returnAmount,
                    $unitId
                );

                // Check if this commodity was part of the original withdrawal
                $originalCommodity = $withdrawalRequest->commodities->where('id', $commodityId)->first();
                
                if ($originalCommodity) {
                    // Normal case: commodity was in original withdrawal
                    $originalAmountInMainUnit = $this->commodityUnitService->convertToMainUnit(
                        $originalCommodity,
                        $originalCommodity->pivot->amount,
                        $originalCommodity->pivot->unit_id
                    );
                } else {
                    // Special case: commodity NOT in original withdrawal
                    // Check if it has a shipping correction adjustment (meaning it was shipped but unrecorded)
                    // These are stored as manual_adjustment type with [تصحیح ارسال: کسر] prefix
                    $correctionAdjustment = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)
                        ->where('commodity_id', $commodityId)
                        ->where('reason', 'like', '%[تصحیح ارسال: کسر]%')
                        ->first();

                    if (!$correctionAdjustment) {
                        throw new \Exception(
                            "کالای {$commodity->title} در درخواست اصلی وجود ندارد و در تصحیحات ثبت نشده است. " .
                            "ابتدا باید تصحیحات ارسالی را ثبت کنید."
                        );
                    }

                    // Use the absolute value of the correction adjustment as the basis
                    $originalAmountInMainUnit = abs($correctionAdjustment->amount);
                }

                // Calculate total already returned for this commodity
                $alreadyReturned = \App\Models\InventoryAdjustment::forWithdrawal($withdrawalRequest->id)
                    ->byType('sales_return')
                    ->where('commodity_id', $commodityId)
                    ->sum('amount'); // Already positive

                // Validate return amount doesn't exceed original
                if (($alreadyReturned + $returnAmountInMainUnit) > $originalAmountInMainUnit) {
                    throw new \Exception(
                        "مقدار برگشتی {$commodity->title} بیش از مقدار است. " .
                        "مقدار: {$originalAmountInMainUnit}, قبلاً برگشت داده شده: {$alreadyReturned}"
                    );
                }

                // Add stock back to inventory with sales_return adjustment
                $this->inventoryService->addStockWithAdjustment(
                    $commodityId,
                    $commodity->unit_id, // Use commodity's main unit
                    $returnAmountInMainUnit,
                    'sales_return',
                    $reason ?? 'برگشت از فروش - درخواست شماره ' . $withdrawalRequest->number,
                    $withdrawalRequest
                );
            }

            return true;
        });
    }
}
