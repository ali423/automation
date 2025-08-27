<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\OrderItem;
use App\Models\WithdrawalRequestItem;

/**
 * BoxCalculationService
 * 
 * This service handles box quantity calculations for both Order and WithdrawalRequest models.
 * It eliminates code duplication by providing a centralized way to calculate:
 * - Number of boxes needed
 * - Pieces per box
 * - Remaining pieces
 * - Unit conversions
 * 
 * Usage:
 * - For Orders: $service->getOrderBoxQuantities($order->orderItems)
 * - For Withdrawals: $service->getWithdrawalBoxQuantities($withdrawal->commodities)
 */

class BoxCalculationService extends BaseService
{
    /**
     * Calculate box quantities for a collection of items
     *
     * @param \Illuminate\Support\Collection $items
     * @param string $itemType 'order' or 'withdrawal'
     * @return array
     */
    public function calculateBoxQuantities($items, $itemType = 'order')
    {
        $boxQuantities = [];
        
        foreach ($items as $item) {
            $commodity = $this->getCommodityFromItem($item, $itemType);
            if (!$commodity) continue;
            
            $amount = $this->getAmountFromItem($item, $itemType);
            $unitId = $this->getUnitIdFromItem($item, $itemType);
            
            $piecesPerBox = $commodity->pieces_per_box ?? 1;
            
            // Convert to pieces first if needed
            $amountInPieces = $this->convertToPieces($commodity, $amount, $unitId);
            
            if ($amountInPieces !== null) {
                $boxes = floor($amountInPieces / $piecesPerBox);
                $remainingPieces = $amountInPieces % $piecesPerBox;
                
                $boxData = [
                    'commodity_title' => $commodity->title,
                    'original_amount' => $amount,
                    'pieces_amount' => $amountInPieces,
                    'pieces_per_box' => $piecesPerBox,
                    'boxes' => $boxes,
                    'remaining_pieces' => $remainingPieces,
                    'can_calculate' => true
                ];
                
                // Add withdrawal-specific fields if needed
                if ($itemType === 'withdrawal') {
                    $boxData['original_unit'] = $this->getOriginalUnitFromItem($item, $itemType);
                    $boxData['total_pieces'] = $amountInPieces;
                }
                
                $boxQuantities[$commodity->id] = $boxData;
            } else {
                $boxData = [
                    'commodity_title' => $commodity->title,
                    'original_amount' => $amount,
                    'pieces_amount' => null,
                    'pieces_per_box' => $piecesPerBox,
                    'boxes' => null,
                    'remaining_pieces' => null,
                    'can_calculate' => false
                ];
                
                // Add withdrawal-specific fields if needed
                if ($itemType === 'withdrawal') {
                    $boxData['original_unit'] = $this->getOriginalUnitFromItem($item, $itemType);
                    $boxData['total_pieces'] = null;
                }
                
                $boxQuantities[$commodity->id] = $boxData;
            }
        }
        
        return $boxQuantities;
    }
    
    /**
     * Get commodity from item based on type
     *
     * @param mixed $item
     * @param string $itemType
     * @return Commodity|null
     */
    private function getCommodityFromItem($item, $itemType)
    {
        if ($itemType === 'order') {
            return $item->commodity;
        } elseif ($itemType === 'withdrawal') {
            return $item; // In withdrawal, the item IS the commodity
        }
        
        return null;
    }
    
    /**
     * Get amount from item based on type
     *
     * @param mixed $item
     * @param string $itemType
     * @return float
     */
    private function getAmountFromItem($item, $itemType)
    {
        if ($itemType === 'order') {
            return $item->commodity_amount;
        } elseif ($itemType === 'withdrawal') {
            return $item->pivot->amount; // In withdrawal, amount is in pivot
        }
        
        return 0;
    }
    
    /**
     * Get unit ID from item based on type
     *
     * @param mixed $item
     * @param string $itemType
     * @return int
     */
    private function getUnitIdFromItem($item, $itemType)
    {
        if ($itemType === 'order') {
            return $item->unit_id;
        } elseif ($itemType === 'withdrawal') {
            return $item->pivot->unit_id; // In withdrawal, unit_id is in pivot
        }
        
        return 0;
    }
    
    /**
     * Get original unit from item based on type
     *
     * @param mixed $item
     * @param string $itemType
     * @return mixed|null
     */
    private function getOriginalUnitFromItem($item, $itemType)
    {
        if ($itemType === 'order') {
            return null; // Orders don't need this field
        } elseif ($itemType === 'withdrawal') {
            return $item->pivot->unit; // In withdrawal, unit object is in pivot
        }
        
        return null;
    }
    
    /**
     * Convert amount to pieces for box calculations
     *
     * @param Commodity $commodity
     * @param float $amount
     * @param int $unitId
     * @return float|null
     */
    private function convertToPieces(Commodity $commodity, $amount, $unitId)
    {
        // If the selected unit is the main unit, we can use the amount directly
        if ($unitId === $commodity->unit_id) {
            return $amount;
        }
        
        // Convert through main unit if possible
        $commodityUnitService = app(CommodityUnitService::class);
        $amountInMainUnit = $commodityUnitService->convertToMainUnit($commodity, $amount, $unitId);
        
        if ($amountInMainUnit === null) {
            return null;
        }
        
        // For box calculations, we'll use the main unit amount as the "pieces" equivalent
        return $amountInMainUnit;
    }
    
    /**
     * Get box quantities for order items
     *
     * @param \Illuminate\Support\Collection $orderItems
     * @return array
     */
    public function getOrderBoxQuantities($orderItems)
    {
        return $this->calculateBoxQuantities($orderItems, 'order');
    }
    
    /**
     * Get box quantities for withdrawal request items
     *
     * @param \Illuminate\Support\Collection $withdrawalItems
     * @return array
     */
    public function getWithdrawalBoxQuantities($withdrawalItems)
    {
        return $this->calculateBoxQuantities($withdrawalItems, 'withdrawal');
    }
}
