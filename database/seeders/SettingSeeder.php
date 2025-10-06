<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // VAT Rate Setting
        Setting::create([
            'key' => 'vat_rate',
            'name' => 'نرخ مالیات ارزش افزوده',
            'value' => '10',
            'type' => 'number',
            'description' => 'نرخ مالیات ارزش افزوده به صورت درصد (مثال: 10 برای 10%)',
            'is_active' => true
        ]);
    }
}
