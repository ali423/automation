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

            //warehouse permissions
            ['title'=>'create_warehouse', 'name'=>'اضافه کردن انبار '],
            ['title'=>'read_warehouse', 'name'=>'دیدن انبار'],
            ['title'=>'edit_warehouse', 'name'=>'ویرایش انبار '],
            ['title'=>'delete_warehouse', 'name'=>'حذف انبار '],

            //withdrawal permissions
            ['title'=>'create_withdrawal', 'name'=>'اضافه کردن فروش کالا '],
            ['title'=>'read_withdrawal', 'name'=>'دیدن فروش کالا'],
            ['title'=>'edit_withdrawal', 'name'=>'ویرایش فروش کالا '],
            ['title'=>'delete_withdrawal', 'name'=>'حذف فروش کالا '],
            ['title'=>'status_withdrawal', 'name'=>'تغییر وضعیت درخواست فروش'],

            //activity permissions
            ['title'=>'read_activity', 'name'=>'مشاهده فعالیت ها'],

            //unit permissions
            ['title'=>'create_unit', 'name'=>'اضافه کردن واحد'],
            ['title'=>'read_unit', 'name'=>'دیدن واحد'],
            ['title'=>'edit_unit', 'name'=>'ویرایش واحد'],
            ['title'=>'delete_unit', 'name'=>'حذف واحد'],

            //inventory permissions
            ['title'=>'view_inventory', 'name'=>'مشاهده موجودی'],
            ['title'=>'create_inventory', 'name'=>'اضافه کردن موجودی'],
            ['title'=>'edit_inventory', 'name'=>'ویرایش موجودی'],
            ['title'=>'delete_inventory', 'name'=>'حذف موجودی'],
            ['title'=>'manage_inventory_commodities', 'name'=>'مدیریت کالاهای موجودی'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['title' => $permission['title']],
                ['name' => $permission['name']]
            );
        }
    }
}
