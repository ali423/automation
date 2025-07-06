<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\Inventory;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create units
        $kgUnit = Unit::firstOrCreate(['name' => 'kg']);
        $kegUnit = Unit::firstOrCreate(['name' => 'keg']);
        $literUnit = Unit::firstOrCreate(['name' => 'liter']);

        // Get some commodities
        $commodities = Commodity::take(5)->get();

        if ($commodities->isEmpty()) {
            $this->command->info('No commodities found. Please run CommoditySeeder first.');
            return;
        }

        foreach ($commodities as $commodity) {
            // Only create or update a single inventory record per commodity/unit
            $basePrice = rand(1000, 5000);
            Inventory::updateOrCreate(
                [
                    'commodity_id' => $commodity->id,
                    'unit_id' => $commodity->unit_id,
                ],
                [
                    'amount' => rand(100, 500),
                    'purchase_price' => $basePrice,
                    'sale_price' => $basePrice * 1.2,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Inventory data seeded successfully!');
    }
} 