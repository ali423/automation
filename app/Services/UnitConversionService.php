<?php

namespace App\Services;

use App\Models\UnitConversion;
use App\Models\Commodity;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class UnitConversionService extends BaseService
{
    /**
     * Create a new unit conversion.
     *
     * @param array $data
     * @return UnitConversion
     */
    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            return UnitConversion::updateOrCreate(
                [
                    'commodity_id' => $data['commodity_id'],
                    'from_unit_id' => $data['from_unit_id'],
                    'to_unit_id' => $data['to_unit_id'],
                ],
                [
                    'conversion_rate' => $data['conversion_rate'],
                ]
            );
        });
    }

    /**
     * Update an existing unit conversion.
     *
     * @param UnitConversion $unitConversion
     * @param array $data
     * @return bool
     */
    public function update(UnitConversion $unitConversion, $data)
    {
        return DB::transaction(function () use ($unitConversion, $data) {
            return $unitConversion->update([
                'from_unit_id' => $data['from_unit_id'],
                'to_unit_id' => $data['to_unit_id'],
                'conversion_rate' => $data['conversion_rate'],
            ]);
        });
    }

    /**
     * Delete a unit conversion.
     *
     * @param UnitConversion $unitConversion
     * @return bool|null
     */
    public function delete(UnitConversion $unitConversion)
    {
        return DB::transaction(function () use ($unitConversion) {
            return $unitConversion->delete();
        });
    }

    /**
     * Convert amount from one unit to another for a specific commodity.
     *
     * @param float $amount
     * @param int $fromUnitId
     * @param int $toUnitId
     * @param int $commodityId
     * @return float|null
     */
    public function convert($amount, $fromUnitId, $toUnitId, $commodityId)
    {
        // If units are the same, return the same amount
        if ($fromUnitId === $toUnitId) {
            return $amount;
        }

        $conversion = $this->getConversionRate($fromUnitId, $toUnitId, $commodityId);
        
        if ($conversion) {
            return $amount * $conversion;
        }

        // Try reverse conversion
        $reverseConversion = $this->getConversionRate($toUnitId, $fromUnitId, $commodityId);
        
        if ($reverseConversion) {
            return $amount / $reverseConversion;
        }

        return null; // No conversion found
    }

    /**
     * Get conversion rate from one unit to another for a specific commodity.
     *
     * @param int $fromUnitId
     * @param int $toUnitId
     * @param int $commodityId
     * @return float|null
     */
    public function getConversionRate($fromUnitId, $toUnitId, $commodityId)
    {
        $conversion = UnitConversion::where([
            'commodity_id' => $commodityId,
            'from_unit_id' => $fromUnitId,
            'to_unit_id' => $toUnitId,
        ])->first();

        return $conversion ? $conversion->conversion_rate : null;
    }

    /**
     * Add or update a conversion rate.
     *
     * @param int $commodityId
     * @param int $fromUnitId
     * @param int $toUnitId
     * @param float $rate
     * @return UnitConversion
     */
    public function addConversion($commodityId, $fromUnitId, $toUnitId, $rate)
    {
        return $this->create([
            'commodity_id' => $commodityId,
            'from_unit_id' => $fromUnitId,
            'to_unit_id' => $toUnitId,
            'conversion_rate' => $rate,
        ]);
    }

    /**
     * Get all conversions for a specific commodity.
     *
     * @param int $commodityId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getConversionsForCommodity($commodityId)
    {
        return UnitConversion::where('commodity_id', $commodityId)
            ->with(['fromUnit', 'toUnit'])
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Check if a conversion exists.
     *
     * @param int $fromUnitId
     * @param int $toUnitId
     * @param int $commodityId
     * @return bool
     */
    public function conversionExists($fromUnitId, $toUnitId, $commodityId)
    {
        return UnitConversion::where([
            'commodity_id' => $commodityId,
            'from_unit_id' => $fromUnitId,
            'to_unit_id' => $toUnitId,
        ])->exists();
    }

    /**
     * Get a query builder for unit conversions with relations loaded.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function queryWithRelations()
    {
        return UnitConversion::with(['fromUnit', 'toUnit', 'commodity']);
    }
} 