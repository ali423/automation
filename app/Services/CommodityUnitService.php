<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Support\Collection;

class CommodityUnitService extends BaseService
{
    /**
     * Get all selectable units for a commodity including main unit and related units
     *
     * @param Commodity $commodity
     * @return Collection
     */
    public function getSelectableUnits(Commodity $commodity): Collection
    {
        $units = collect();
        
        // Add the main unit of the commodity
        if ($commodity->unit) {
            $units->push($commodity->unit);
        } else {
            return $units; // Return empty collection if no main unit
        }
        
        // Get all related units through unit conversions
        $relatedUnits = $this->getRelatedUnits($commodity);
        $units = $units->merge($relatedUnits);
        
        $result = $units->unique('id');
        
        return $result;
    }
    
    /**
     * Get all units that can be converted from or to the main unit
     *
     * @param Commodity $commodity
     * @return Collection
     */
    public function getRelatedUnits(Commodity $commodity): Collection
    {
        if (!$commodity->unit) {
            return collect();
        }
        
        $mainUnitId = $commodity->unit_id;
        
        // Get all conversions where the main unit is either from_unit or to_unit
        $conversions = UnitConversion::where('commodity_id', $commodity->id)
            ->where(function ($query) use ($mainUnitId) {
                $query->where('from_unit_id', $mainUnitId)
                      ->orWhere('to_unit_id', $mainUnitId);
            })
            ->with(['fromUnit', 'toUnit'])
            ->get();
        
        $relatedUnits = collect();
        
        foreach ($conversions as $conversion) {
            // Add the "from" unit if it's not the main unit
            if ($conversion->from_unit_id !== $mainUnitId) {
                $relatedUnits->push($conversion->fromUnit);
            }
            
            // Add the "to" unit if it's not the main unit
            if ($conversion->to_unit_id !== $mainUnitId) {
                $relatedUnits->push($conversion->toUnit);
            }
        }
        
        $result = $relatedUnits->unique('id');
        
        return $result;
    }
    
    /**
     * Convert amount from selected unit to main unit
     *
     * @param Commodity $commodity
     * @param float $amount
     * @param int $selectedUnitId
     * @return float|null
     */
    public function convertToMainUnit(Commodity $commodity, float $amount, int $selectedUnitId): ?float
    {
        // Validate input parameters
        if (!is_numeric($amount) || !is_numeric($selectedUnitId) || !$commodity) {
            return null;
        }
        
        // If selected unit is the main unit, return the amount as is
        if ($selectedUnitId === $commodity->unit_id) {
            return (float) $amount;
        }
        
        // Use the existing UnitConversionService to convert
        $unitConversionService = app(UnitConversionService::class);
        $result = $unitConversionService->convert(
            $amount,
            $selectedUnitId,
            $commodity->unit_id,
            $commodity->id
        );
        
        return is_numeric($result) ? (float) $result : null;
    }
    
    /**
     * Check if a unit is selectable for a commodity
     *
     * @param Commodity $commodity
     * @param int $unitId
     * @return bool
     */
    public function isUnitSelectable(Commodity $commodity, int $unitId): bool
    {
        $selectableUnits = $this->getSelectableUnits($commodity);
        return $selectableUnits->contains('id', $unitId);
    }
} 