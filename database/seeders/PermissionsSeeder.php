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
    }
}
