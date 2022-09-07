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

        //user permissions
        Permission::query()
            ->insert([
                ['title'=>'create_user', 'name'=>'اضافه کردن کاربر '],
                ['title'=>'read_user', 'name'=>'دیدن کاربر'],
                ['title'=>'edit_user', 'name'=>'ویرایش کاربر '],
                ['title'=>'delete_user', 'name'=>'حذف کاربر '],
            ]);

        //role permissions
        Permission::query()
            ->insert([
                ['title'=>'create_role', 'name'=>'اضافه کردن نقش '],
                ['title'=>'read_role', 'name'=>'دیدن نقش'],
                ['title'=>'edit_role', 'name'=>'ویرایش نقش '],
                ['title'=>'delete_role', 'name'=>'حذف نقش '],
            ]);

        //customer permissions
        Permission::query()
            ->insert([
                ['title'=>'create_customer', 'name'=>'اضافه کردن مشتری '],
                ['title'=>'read_customer', 'name'=>'دیدن مشتری'],
                ['title'=>'edit_customer', 'name'=>'ویرایش مشتری '],
                ['title'=>'delete_customer', 'name'=>'حذف مشتری '],
            ]);

        //seller permissions
        Permission::query()
            ->insert([
                ['title'=>'create_seller', 'name'=>'اضافه کردن فروشنده '],
                ['title'=>'read_seller', 'name'=>'دیدن فروشنده'],
                ['title'=>'edit_seller', 'name'=>'ویرایش فروشنده '],
                ['title'=>'delete_seller', 'name'=>'حذف فروشنده '],
            ]);

        //commodity permissions
        Permission::query()
            ->insert([
                ['title'=>'create_commodity', 'name'=>'اضافه کردن کالا '],
                ['title'=>'read_commodity', 'name'=>'دیدن کالا'],
                ['title'=>'edit_commodity', 'name'=>'ویرایش کالا '],
                ['title'=>'delete_commodity', 'name'=>'حذف کالا '],
            ]);

        //importing permissions
        Permission::query()
            ->insert([
                ['title'=>'create_importing', 'name'=>'اضافه کردن ورود کالا '],
                ['title'=>'read_importing', 'name'=>'دیدن ورود کالا'],
                ['title'=>'edit_importing', 'name'=>'ویرایش ورود کالا '],
                ['title'=>'delete_importing', 'name'=>'حذف ورود کالا '],
                ['title'=>'status_importing', 'name'=>'تغییر وضعیت درخواست ورود'],
            ]);

        //order permissions
        Permission::query()
            ->insert([
                ['title'=>'create_order', 'name'=>'اضافه کردن سفارش '],
                ['title'=>'read_order', 'name'=>'دیدن سفارش'],
                ['title'=>'edit_order', 'name'=>'ویرایش سفارش '],
                ['title'=>'delete_order', 'name'=>'حذف سفارش '],
            ]);

        //warehouse permissions
        Permission::query()
            ->insert([
                ['title'=>'create_warehouse', 'name'=>'اضافه کردن انبار '],
                ['title'=>'read_warehouse', 'name'=>'دیدن انبار'],
                ['title'=>'edit_warehouse', 'name'=>'ویرایش انبار '],
                ['title'=>'delete_warehouse', 'name'=>'حذف انبار '],
            ]);

        //withdrawal permissions
        Permission::query()
            ->insert([
                ['title'=>'create_withdrawal', 'name'=>'اضافه کردن فروش کالا '],
                ['title'=>'read_withdrawal', 'name'=>'دیدن فروش کالا'],
                ['title'=>'edit_withdrawal', 'name'=>'ویرایش فروش کالا '],
                ['title'=>'delete_withdrawal', 'name'=>'حذف فروش کالا '],
                ['title'=>'status_withdrawal', 'name'=>'تغییر وضعیت درخواست فروش'],
            ]);

        //activity permissions
        Permission::query()
            ->insert([
                ['title'=>'read_activity', 'name'=>'مشاهده فعالیت ها'],
            ]);
    }
}
