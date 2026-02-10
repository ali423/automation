<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            //user permissions
            ['title'=>'create_user', 'name'=>'اضافه کردن کاربر '],
            ['title'=>'read_user', 'name'=>'دیدن کاربر'],
            ['title'=>'edit_user', 'name'=>'ویرایش کاربر '],
            ['title'=>'delete_user', 'name'=>'حذف کاربر '],

            //role permissions
            ['title'=>'create_role', 'name'=>'اضافه کردن نقش '],
            ['title'=>'read_role', 'name'=>'دیدن نقش'],
            ['title'=>'edit_role', 'name'=>'ویرایش نقش '],
            ['title'=>'delete_role', 'name'=>'حذف نقش '],

            //customer permissions
            ['title'=>'create_customer', 'name'=>'اضافه کردن مشتری '],
            ['title'=>'read_customer', 'name'=>'دیدن مشتری'],
            ['title'=>'edit_customer', 'name'=>'ویرایش مشتری '],
            ['title'=>'delete_customer', 'name'=>'حذف مشتری '],

            //seller permissions
            ['title'=>'create_seller', 'name'=>'اضافه کردن فروشنده '],
            ['title'=>'read_seller', 'name'=>'دیدن فروشنده'],
            ['title'=>'edit_seller', 'name'=>'ویرایش فروشنده '],
            ['title'=>'delete_seller', 'name'=>'حذف فروشنده '],

            //commodity permissions
            ['title'=>'create_commodity', 'name'=>'اضافه کردن کالا '],
            ['title'=>'read_commodity', 'name'=>'دیدن کالا'],
            ['title'=>'edit_commodity', 'name'=>'ویرایش کالا '],
            ['title'=>'delete_commodity', 'name'=>'حذف کالا '],

            //attribute permissions
            ['title'=>'create_attribute', 'name'=>'اضافه کردن ویژگی '],
            ['title'=>'read_attribute', 'name'=>'دیدن ویژگی'],
            ['title'=>'edit_attribute', 'name'=>'ویرایش ویژگی '],
            ['title'=>'delete_attribute', 'name'=>'حذف ویژگی '],

            //importing permissions
            ['title'=>'create_importing', 'name'=>'اضافه کردن ورود کالا '],
            ['title'=>'read_importing', 'name'=>'دیدن ورود کالا'],
            ['title'=>'edit_importing', 'name'=>'ویرایش ورود کالا '],
            ['title'=>'delete_importing', 'name'=>'حذف ورود کالا '],
            ['title'=>'status_importing', 'name'=>'تغییر وضعیت درخواست ورود'],

            //order permissions
            ['title'=>'create_order', 'name'=>'اضافه کردن سفارش '],
            ['title'=>'read_order', 'name'=>'دیدن سفارش'],
            ['title'=>'edit_order', 'name'=>'ویرایش سفارش '],
            ['title'=>'delete_order', 'name'=>'حذف سفارش '],

            //withdrawal permissions
            ['title'=>'create_withdrawal', 'name'=>'اضافه کردن فروش کالا '],
            ['title'=>'read_withdrawal', 'name'=>'دیدن فروش کالا'],
            ['title'=>'edit_withdrawal', 'name'=>'ویرایش فروش کالا '],
            ['title'=>'delete_withdrawal', 'name'=>'حذف فروش کالا '],
            ['title'=>'status_withdrawal', 'name'=>'تغییر وضعیت درخواست فروش'],
            ['title'=>'cancel_withdrawal', 'name'=>'لغو درخواست فروش'],

            //activity permissions
            ['title'=>'read_activity', 'name'=>'مشاهده فعالیت ها'],

            //unit permissions
            ['title'=>'create_unit', 'name'=>'اضافه کردن واحد'],
            ['title'=>'read_unit', 'name'=>'دیدن واحد'],
            ['title'=>'edit_unit', 'name'=>'ویرایش واحد'],
            ['title'=>'delete_unit', 'name'=>'حذف واحد'],

            //unit conversion permissions
            ['title'=>'create_unit_conversion', 'name'=>'اضافه کردن تبدیل واحد'],
            ['title'=>'read_unit_conversion', 'name'=>'دیدن تبدیل واحد'],
            ['title'=>'edit_unit_conversion', 'name'=>'ویرایش تبدیل واحد'],
            ['title'=>'delete_unit_conversion', 'name'=>'حذف تبدیل واحد'],

            //production request permissions
            ['title'=>'create_production', 'name'=>'اضافه کردن درخواست تولید'],
            ['title'=>'read_production', 'name'=>'دیدن درخواست تولید'],
            ['title'=>'edit_production', 'name'=>'ویرایش درخواست تولید'],
            ['title'=>'delete_production', 'name'=>'حذف درخواست تولید'],
            ['title'=>'status_production', 'name'=>'تغییر وضعیت درخواست تولید'],

                //inventory permissions
            ['title'=>'create_inventory', 'name'=>'اضافه کردن موجودی'],
            ['title'=>'read_inventory', 'name'=>'دیدن موجودی'],
            ['title'=>'edit_inventory', 'name'=>'ویرایش موجودی'],
            ['title'=>'delete_inventory', 'name'=>'حذف موجودی'],
            ['title'=>'manage_inventory', 'name'=>'مدیریت موجودی'],

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['title' => $permission['title']],
                ['name' => $permission['name']]
            );
        }
    }
}
