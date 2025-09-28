<?php

function getSystemModelsSymbol(){
    return array_map(function ($item){
        return  substr($item, 11);
    },array_keys(config('enums.models')));
}

if (!function_exists('calculate_weight')) {
    /**
     * Calculate the total weight in kg for a given amount of a commodity
     *
     * @param \App\Models\Commodity $commodity The commodity
     * @param float $amount The amount of the commodity
     * @param int|null $unitId The unit ID (if null, uses main unit)
     * @return float|null The total weight in kg
     */
    function calculate_weight($commodity, $amount, $unitId = null)
    {
        if (!$commodity || !$commodity->weight_per_unit) {
            return null;
        }

        // If no unit specified or unit is the main unit, return direct calculation
        if (!$unitId || $unitId == $commodity->unit_id) {
            return $amount * $commodity->weight_per_unit;
        }

        // Convert to main unit first, then calculate weight
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
        $amountInMainUnit = $commodityUnitService->convertToMainUnit($commodity, $amount, $unitId);
        
        if ($amountInMainUnit === null) {
            return null;
        }

        return $amountInMainUnit * $commodity->weight_per_unit;
    }
}

if (!function_exists('calculate_order_total_weight')) {
    /**
     * Calculate the total weight in kg for all items in an order
     *
     * @param \App\Models\Order $order The order
     * @return float The total weight in kg
     */
    function calculate_order_total_weight($order)
    {
        $totalWeight = 0;
        foreach ($order->orderItems as $item) {
            $weight = calculate_weight($item->commodity, $item->commodity_amount, $item->unit_id);
            if ($weight !== null) {
                $totalWeight += $weight;
            }
        }
        return $totalWeight;
    }
}

if (!function_exists('calculate_withdrawal_request_total_weight')) {
    /**
     * Calculate the total weight in kg for all commodities in a withdrawal request
     *
     * @param \App\Models\WithdrawalRequest $withdrawalRequest The withdrawal request
     * @return float The total weight in kg
     */
    function calculate_withdrawal_request_total_weight($withdrawalRequest)
    {
        $totalWeight = 0;
        foreach ($withdrawalRequest->commodities as $commodity) {
            $weight = calculate_weight($commodity, $commodity->pivot->amount, $commodity->pivot->unit_id);
            if ($weight !== null) {
                $totalWeight += $weight;
            }
        }
        return $totalWeight;
    }
}

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key The setting key
     * @param mixed $default The default value if setting not found
     * @return mixed The setting value
     */
    function setting($key, $default = null)
    {
        return \App\Models\Setting::getValue($key, $default);
    }
}

if (!function_exists('vat_percentage')) {
    /**
     * Get the current VAT rate as percentage
     *
     * @return float The VAT rate as percentage (e.g., 10 for 10%)
     */
    function vat_percentage()
    {
        return setting('vat_rate', 10);
    }
}

if (!function_exists('vat_rate')) {
    /**
     * Get the current VAT rate as decimal
     *
     * @return float The VAT rate as decimal (e.g., 0.1 for 10%)
     */
    function vat_rate()
    {
        return vat_percentage() / 100;
    }
}

if (!function_exists('default_product_identifier')) {
    /**
     * Get the default product identifier
     *
     * @return string The default product identifier
     */
    function default_product_identifier()
    {
        return '2923649785421';
    }
}