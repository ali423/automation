<?php

namespace App\Services;

class BaseService
{
    public function calculateCommodityAmount($amount,$unit){
        switch ($unit) {
            case 'keg':
                return round($amount*185,2);
            case 'kg':
                return $amount;
            case 'twenty_liters':
                return round($amount*17.8,2);
        }
    }
}
