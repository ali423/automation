<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::create(['name' => 'کیلوگرم', 'symbol' => 'kg']);
        Unit::create(['name' => 'لیتر', 'symbol' => 'L']);
        Unit::create(['name' => 'عدد', 'symbol' => 'pcs']);
        Unit::create(['name' => 'کارتن', 'symbol' => 'box']);
    }
}
