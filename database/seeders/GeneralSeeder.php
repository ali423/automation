<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\Customer;
use App\Models\Seller;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class GeneralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get units for reference
        $kgUnit = Unit::where('symbol', 'kg')->first();
        $literUnit = Unit::where('symbol', 'L')->first();
        $pieceUnit = Unit::where('symbol', 'pcs')->first();

        // Create Materials (type: material)
        $materials = [
            [
                'number' => 1001,
                'title' => 'روغن پایه',
                'sales_price' => 8.50,
                'purchase_price' => 6.20,
                'warning_limit' => 5000,
                'type' => 'material',
                'unit_id' => $literUnit->id,
            ],
            [
                'number' => 1002,
                'title' => 'بسته افزودنی',
                'sales_price' => 45.00,
                'purchase_price' => 35.00,
                'warning_limit' => 500,
                'type' => 'material',
                'unit_id' => $kgUnit->id,
            ],
            [
                'number' => 1003,
                'title' => 'تغلیظ کننده',
                'sales_price' => 25.00,
                'purchase_price' => 18.00,
                'warning_limit' => 300,
                'type' => 'material',
                'unit_id' => $kgUnit->id,
            ],
            [
                'number' => 1004,
                'title' => 'افزودنی ضد سایش',
                'sales_price' => 60.00,
                'purchase_price' => 45.00,
                'warning_limit' => 200,
                'type' => 'material',
                'unit_id' => $kgUnit->id,
            ],
            [
                'number' => 1005,
                'title' => 'افزودنی پاک کننده',
                'sales_price' => 35.00,
                'purchase_price' => 28.00,
                'warning_limit' => 400,
                'type' => 'material',
                'unit_id' => $kgUnit->id,
            ],
            [
                'number' => 1006,
                'title' => 'افزودنی پراکنده کننده',
                'sales_price' => 40.00,
                'purchase_price' => 32.00,
                'warning_limit' => 350,
                'type' => 'material',
                'unit_id' => $kgUnit->id,
            ],
            [
                'number' => 1007,
                'title' => 'کاهنده نقطه ریزش',
                'sales_price' => 55.00,
                'purchase_price' => 42.00,
                'warning_limit' => 150,
                'type' => 'material',
                'unit_id' => $kgUnit->id,
            ],
            [
                'number' => 1008,
                'title' => 'افزودنی ضد اکسیداسیون',
                'sales_price' => 50.00,
                'purchase_price' => 38.00,
                'warning_limit' => 250,
                'type' => 'material',
                'unit_id' => $kgUnit->id,
            ],
            [
                'number' => 1009,
                'title' => 'کاهنده اصطکاک',
                'sales_price' => 70.00,
                'purchase_price' => 55.00,
                'warning_limit' => 100,
                'type' => 'material',
                'unit_id' => $kgUnit->id,
            ],
            [
                'number' => 1010,
                'title' => 'بازدارنده خوردگی',
                'sales_price' => 65.00,
                'purchase_price' => 50.00,
                'warning_limit' => 120,
                'type' => 'material',
                'unit_id' => $kgUnit->id,
            ],
        ];

        // Create materials
        $createdMaterials = [];
        foreach ($materials as $material) {
            $createdMaterials[] = Commodity::create($material);
        }

        // Create Products (type: product)
        $products = [
            [
                'number' => 2001,
                'title' => 'روغن موتور 5W-30',
                'sales_price' => 45.00,
                'purchase_price' => null, // Will be calculated from materials
                'warning_limit' => 500,
                'type' => 'product',
                'unit_id' => $literUnit->id,
            ],
            [
                'number' => 2002,
                'title' => 'روغن موتور 10W-40',
                'sales_price' => 42.00,
                'purchase_price' => null, // Will be calculated from materials
                'warning_limit' => 400,
                'type' => 'product',
                'unit_id' => $literUnit->id,
            ],
            [
                'number' => 2003,
                'title' => 'روغن موتور 15W-50',
                'sales_price' => 48.00,
                'purchase_price' => null, // Will be calculated from materials
                'warning_limit' => 300,
                'type' => 'product',
                'unit_id' => $literUnit->id,
            ],
            [
                'number' => 2004,
                'title' => 'روغن گیربکس',
                'sales_price' => 55.00,
                'purchase_price' => null, // Will be calculated from materials
                'warning_limit' => 200,
                'type' => 'product',
                'unit_id' => $literUnit->id,
            ],
            [
                'number' => 2005,
                'title' => 'روغن هیدرولیک',
                'sales_price' => 38.00,
                'purchase_price' => null, // Will be calculated from materials
                'warning_limit' => 350,
                'type' => 'product',
                'unit_id' => $literUnit->id,
            ],
            [
                'number' => 2006,
                'title' => 'روغن دنده',
                'sales_price' => 52.00,
                'purchase_price' => null, // Will be calculated from materials
                'warning_limit' => 250,
                'type' => 'product',
                'unit_id' => $literUnit->id,
            ],
            [
                'number' => 2007,
                'title' => 'روغن ترمز',
                'sales_price' => 28.00,
                'purchase_price' => null, // Will be calculated from materials
                'warning_limit' => 150,
                'type' => 'product',
                'unit_id' => $literUnit->id,
            ],
            [
                'number' => 2008,
                'title' => 'روغن فرمان',
                'sales_price' => 32.00,
                'purchase_price' => null, // Will be calculated from materials
                'warning_limit' => 180,
                'type' => 'product',
                'unit_id' => $literUnit->id,
            ],
        ];

        // Create products
        $createdProducts = [];
        foreach ($products as $product) {
            $createdProducts[] = Commodity::create($product);
        }

        // Create product formulas (materials used in products) - Unit-based amounts
        $formulas = [
            // Engine Oil 5W-30 formula (for 1 kg of product)
            [
                'product_id' => $createdProducts[0]->id, // Engine Oil 5W-30
                'material_id' => $createdMaterials[0]->id, // Base Oil - 0.85 kg
                'amount' => 0.85,
                'unit_id' => $createdProducts[0]->unit_id, // Use product's unit (kg)
            ],
            [
                'product_id' => $createdProducts[0]->id, // Engine Oil 5W-30
                'material_id' => $createdMaterials[1]->id, // Additive Package - 0.08 kg
                'amount' => 0.08,
                'unit_id' => $createdProducts[0]->unit_id,
            ],
            [
                'product_id' => $createdProducts[0]->id, // Engine Oil 5W-30
                'material_id' => $createdMaterials[2]->id, // Viscosity Modifier - 0.05 kg
                'amount' => 0.05,
                'unit_id' => $createdProducts[0]->unit_id,
            ],
            [
                'product_id' => $createdProducts[0]->id, // Engine Oil 5W-30
                'material_id' => $createdMaterials[3]->id, // Anti-Wear Additive - 0.02 kg
                'amount' => 0.02,
                'unit_id' => $createdProducts[0]->unit_id,
            ],

            // Engine Oil 10W-40 formula (for 1 kg of product)
            [
                'product_id' => $createdProducts[1]->id, // Engine Oil 10W-40
                'material_id' => $createdMaterials[0]->id, // Base Oil - 0.80 kg
                'amount' => 0.80,
                'unit_id' => $createdProducts[1]->unit_id,
            ],
            [
                'product_id' => $createdProducts[1]->id, // Engine Oil 10W-40
                'material_id' => $createdMaterials[1]->id, // Additive Package - 0.10 kg
                'amount' => 0.10,
                'unit_id' => $createdProducts[1]->unit_id,
            ],
            [
                'product_id' => $createdProducts[1]->id, // Engine Oil 10W-40
                'material_id' => $createdMaterials[2]->id, // Viscosity Modifier - 0.08 kg
                'amount' => 0.08,
                'unit_id' => $createdProducts[1]->unit_id,
            ],
            [
                'product_id' => $createdProducts[1]->id, // Engine Oil 10W-40
                'material_id' => $createdMaterials[3]->id, // Anti-Wear Additive - 0.02 kg
                'amount' => 0.02,
                'unit_id' => $createdProducts[1]->unit_id,
            ],

            // Engine Oil 15W-50 formula (for 1 kg of product)
            [
                'product_id' => $createdProducts[2]->id, // Engine Oil 15W-50
                'material_id' => $createdMaterials[0]->id, // Base Oil - 0.75 kg
                'amount' => 0.75,
                'unit_id' => $createdProducts[2]->unit_id,
            ],
            [
                'product_id' => $createdProducts[2]->id, // Engine Oil 15W-50
                'material_id' => $createdMaterials[1]->id, // Additive Package - 0.12 kg
                'amount' => 0.12,
                'unit_id' => $createdProducts[2]->unit_id,
            ],
            [
                'product_id' => $createdProducts[2]->id, // Engine Oil 15W-50
                'material_id' => $createdMaterials[2]->id, // Viscosity Modifier - 0.10 kg
                'amount' => 0.10,
                'unit_id' => $createdProducts[2]->unit_id,
            ],
            [
                'product_id' => $createdProducts[2]->id, // Engine Oil 15W-50
                'material_id' => $createdMaterials[3]->id, // Anti-Wear Additive - 0.03 kg
                'amount' => 0.03,
                'unit_id' => $createdProducts[2]->unit_id,
            ],

            // Transmission Oil formula (for 1 kg of product)
            [
                'product_id' => $createdProducts[3]->id, // Transmission Oil
                'material_id' => $createdMaterials[0]->id, // Base Oil - 0.70 kg
                'amount' => 0.70,
                'unit_id' => $createdProducts[3]->unit_id,
            ],
            [
                'product_id' => $createdProducts[3]->id, // Transmission Oil
                'material_id' => $createdMaterials[1]->id, // Additive Package - 0.15 kg
                'amount' => 0.15,
                'unit_id' => $createdProducts[3]->unit_id,
            ],
            [
                'product_id' => $createdProducts[3]->id, // Transmission Oil
                'material_id' => $createdMaterials[4]->id, // Detergent Additive - 0.08 kg
                'amount' => 0.08,
                'unit_id' => $createdProducts[3]->unit_id,
            ],
            [
                'product_id' => $createdProducts[3]->id, // Transmission Oil
                'material_id' => $createdMaterials[5]->id, // Dispersant Additive - 0.07 kg
                'amount' => 0.07,
                'unit_id' => $createdProducts[3]->unit_id,
            ],

            // Hydraulic Oil formula (for 1 kg of product)
            [
                'product_id' => $createdProducts[4]->id, // Hydraulic Oil
                'material_id' => $createdMaterials[0]->id, // Base Oil - 0.90 kg
                'amount' => 0.90,
                'unit_id' => $createdProducts[4]->unit_id,
            ],
            [
                'product_id' => $createdProducts[4]->id, // Hydraulic Oil
                'material_id' => $createdMaterials[1]->id, // Additive Package - 0.06 kg
                'amount' => 0.06,
                'unit_id' => $createdProducts[4]->unit_id,
            ],
            [
                'product_id' => $createdProducts[4]->id, // Hydraulic Oil
                'material_id' => $createdMaterials[8]->id, // Antioxidant Additive - 0.04 kg
                'amount' => 0.04,
                'unit_id' => $createdProducts[4]->unit_id,
            ],

            // Gear Oil formula (for 1 kg of product)
            [
                'product_id' => $createdProducts[5]->id, // Gear Oil
                'material_id' => $createdMaterials[0]->id, // Base Oil - 0.65 kg
                'amount' => 0.65,
                'unit_id' => $createdProducts[5]->unit_id,
            ],
            [
                'product_id' => $createdProducts[5]->id, // Gear Oil
                'material_id' => $createdMaterials[1]->id, // Additive Package - 0.20 kg
                'amount' => 0.20,
                'unit_id' => $createdProducts[5]->unit_id,
            ],
            [
                'product_id' => $createdProducts[5]->id, // Gear Oil
                'material_id' => $createdMaterials[3]->id, // Anti-Wear Additive - 0.10 kg
                'amount' => 0.10,
                'unit_id' => $createdProducts[5]->unit_id,
            ],
            [
                'product_id' => $createdProducts[5]->id, // Gear Oil
                'material_id' => $createdMaterials[8]->id, // Antioxidant Additive - 0.05 kg
                'amount' => 0.05,
                'unit_id' => $createdProducts[5]->unit_id,
            ],

            // Brake Fluid formula (for 1 kg of product)
            [
                'product_id' => $createdProducts[6]->id, // Brake Fluid
                'material_id' => $createdMaterials[0]->id, // Base Oil - 0.60 kg
                'amount' => 0.60,
                'unit_id' => $createdProducts[6]->unit_id,
            ],
            [
                'product_id' => $createdProducts[6]->id, // Brake Fluid
                'material_id' => $createdMaterials[1]->id, // Additive Package - 0.25 kg
                'amount' => 0.25,
                'unit_id' => $createdProducts[6]->unit_id,
            ],
            [
                'product_id' => $createdProducts[6]->id, // Brake Fluid
                'material_id' => $createdMaterials[9]->id, // Corrosion Inhibitor - 0.15 kg
                'amount' => 0.15,
                'unit_id' => $createdProducts[6]->unit_id,
            ],

            // Power Steering Fluid formula (for 1 kg of product)
            [
                'product_id' => $createdProducts[7]->id, // Power Steering Fluid
                'material_id' => $createdMaterials[0]->id, // Base Oil - 0.75 kg
                'amount' => 0.75,
                'unit_id' => $createdProducts[7]->unit_id,
            ],
            [
                'product_id' => $createdProducts[7]->id, // Power Steering Fluid
                'material_id' => $createdMaterials[1]->id, // Additive Package - 0.15 kg
                'amount' => 0.15,
                'unit_id' => $createdProducts[7]->unit_id,
            ],
            [
                'product_id' => $createdProducts[7]->id, // Power Steering Fluid
                'material_id' => $createdMaterials[8]->id, // Antioxidant Additive - 0.10 kg
                'amount' => 0.10,
                'unit_id' => $createdProducts[7]->unit_id,
            ],
        ];

        // Create product formulas
        foreach ($formulas as $formula) {
            \DB::table('product_formula')->insert($formula);
        }

        // Create Warehouses
        $warehouses = [
            [
                'title' => 'انبار اصلی',
                'capacity' => 10000,
                'type' => 'hall',
                'status' => 'active',
                'empty_space' => 2000,
            ],
            [
                'title' => 'انبار فرعی',
                'capacity' => 5000,
                'type' => 'hall',
                'status' => 'active',
                'empty_space' => 1000,
            ],
            [
                'title' => 'انبار سرد',
                'capacity' => 3000,
                'type' => 'hall',
                'status' => 'active',
                'empty_space' => 500,
            ],
        ];

        $createdWarehouses = [];
        foreach ($warehouses as $warehouse) {
            $createdWarehouses[] = Warehouse::create($warehouse);
        }

        // Create Sellers
        $sellers = [
            [
                'name' => 'احمد رضایی',
                'mobile' => '09123456789',
                'comp_name' => 'شرکت تجاری روغن رضایی',
                'address' => 'تهران، ایران',
                'zip_code' => '1234567890',
                'phone' => '02112345678',
                'national_code' => '1234567890',
                'economic_code' => '123456789',
            ],
            [
                'name' => 'فاطمه کریمی',
                'mobile' => '09987654321',
                'comp_name' => 'تامین کنندگان نفت کریمی',
                'address' => 'اصفهان، ایران',
                'zip_code' => '9876543210',
                'phone' => '03187654321',
                'national_code' => '0987654321',
                'economic_code' => '987654321',
            ],
            [
                'name' => 'محمد حسینی',
                'mobile' => '09351234567',
                'comp_name' => 'حسینی واردات و صادرات روغن',
                'address' => 'مشهد، ایران',
                'zip_code' => '1122334455',
                'phone' => '05111223344',
                'national_code' => '1122334455',
                'economic_code' => '112233445',
            ],
            [
                'name' => 'علی محمدی',
                'mobile' => '09361234567',
                'comp_name' => 'شرکت روانکار محمدی',
                'address' => 'شیراز، ایران',
                'zip_code' => '2233445566',
                'phone' => '07122334455',
                'national_code' => '2233445566',
                'economic_code' => '223344556',
            ],
        ];

        foreach ($sellers as $seller) {
            Seller::create($seller);
        }

        // Create Customers
        $customers = [
            [
                'name' => 'حسن رحیمی',
                'mobile' => '09111111111',
                'comp_name' => 'تعمیرگاه خودرو رحیمی',
                'address' => 'تهران، ایران',
                'zip_code' => '1111111111',
                'phone' => '02111111111',
                'national_code' => '1111111111',
                'economic_code' => '111111111',
            ],
            [
                'name' => 'سارا احمدی',
                'mobile' => '09222222222',
                'comp_name' => 'نمایندگی خودرو احمدی',
                'address' => 'شیراز، ایران',
                'zip_code' => '2222222222',
                'phone' => '07122222222',
                'national_code' => '2222222222',
                'economic_code' => '222222222',
            ],
            [
                'name' => 'رضا جعفری',
                'mobile' => '09333333333',
                'comp_name' => 'ماشین آلات صنعتی جعفری',
                'address' => 'تبریز، ایران',
                'zip_code' => '3333333333',
                'phone' => '04133333333',
                'national_code' => '3333333333',
                'economic_code' => '333333333',
            ],
            [
                'name' => 'زهرا صالحی',
                'mobile' => '09444444444',
                'comp_name' => 'شرکت ساختمانی صالحی',
                'address' => 'یزد، ایران',
                'zip_code' => '4444444444',
                'phone' => '03544444444',
                'national_code' => '4444444444',
                'economic_code' => '444444444',
            ],
            [
                'name' => 'محمد کریمی',
                'mobile' => '09555555555',
                'comp_name' => 'تجهیزات سنگین کریمی',
                'address' => 'مشهد، ایران',
                'zip_code' => '5555555555',
                'phone' => '05155555555',
                'national_code' => '5555555555',
                'economic_code' => '555555555',
            ],
            [
                'name' => 'فاطمه حسینی',
                'mobile' => '09666666666',
                'comp_name' => 'مدیریت ناوگان حسینی',
                'address' => 'اصفهان، ایران',
                'zip_code' => '6666666666',
                'phone' => '03166666666',
                'national_code' => '6666666666',
                'economic_code' => '666666666',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        // Assign stock to commodities in warehouses
        $stockAssignments = [];

        // Assign materials to warehouses
        foreach ($createdMaterials as $material) {
            foreach ($createdWarehouses as $warehouse) {
                $stockAssignments[] = [
                    'commodity_id' => $material->id,
                    'warehouse_id' => $warehouse->id,
                    'commodity_amount' => rand(100, 2000), // Random stock amount
                    'average_purchase_price' => $material->purchase_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Assign products to warehouses (smaller amounts since they're products)
        foreach ($createdProducts as $product) {
            foreach ($createdWarehouses as $warehouse) {
                $stockAssignments[] = [
                    'commodity_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'commodity_amount' => rand(10, 200), // Smaller amounts for products
                    'average_purchase_price' => null, // Products don't have direct purchase price
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert stock assignments
        \DB::table('commodity_warehouse')->insert($stockAssignments);

        $this->command->info('General seeder completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . count($createdMaterials) . ' materials');
        $this->command->info('- ' . count($createdProducts) . ' products');
        $this->command->info('- ' . count($formulas) . ' product formulas');
        $this->command->info('- ' . count($createdWarehouses) . ' warehouses');
        $this->command->info('- ' . count($sellers) . ' sellers');
        $this->command->info('- ' . count($customers) . ' customers');
        $this->command->info('- ' . count($stockAssignments) . ' stock assignments');
    }
}
