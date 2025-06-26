<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class UnitService extends BaseService
{
    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            return Unit::create([
                'name' => $data['name'],
                'symbol' => $data['symbol'],
            ]);
        });
    }

    public function update(Unit $unit, $data)
    {
        return DB::transaction(function () use ($unit, $data) {
            return $unit->update([
                'name' => $data['name'],
                'symbol' => $data['symbol'],
            ]);
        });
    }

    public function delete(Unit $unit)
    {
        return DB::transaction(function () use ($unit) {
            if ($unit->commodities()->exists()) {
                throw new \Exception('این واحد در حال استفاده توسط کالاها می‌باشد و قابل حذف نیست.');
            }
            return $unit->delete();
        });
    }
}

