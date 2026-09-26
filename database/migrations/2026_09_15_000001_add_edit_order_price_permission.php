<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

class AddEditOrderPricePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permission = Permission::firstOrCreate(
            ['title' => 'edit_order_price'],
            ['name' => 'ویرایش قیمت فروش سفارش']
        );

        $adminRole = Role::where('title', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permission = Permission::where('title', 'edit_order_price')->first();
        if (!$permission) {
            return;
        }

        $permission->roles()->detach();
        $permission->delete();
    }
}
