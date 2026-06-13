<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

class AssignCancelProductionPermissionToRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permission = Permission::firstOrCreate(
            ['title' => 'cancel_production'],
            ['name' => 'لغو درخواست تولید']
        );

        $sourcePermissionTitles = ['status_production', 'cancel_withdrawal'];

        $roleIds = Role::whereHas('permissions', function ($query) use ($sourcePermissionTitles) {
            $query->whereIn('title', $sourcePermissionTitles);
        })->orWhere('title', 'admin')->pluck('id');

        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            if ($role) {
                $role->permissions()->syncWithoutDetaching([$permission->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permission = Permission::where('title', 'cancel_production')->first();
        if (!$permission) {
            return;
        }

        foreach (Role::all() as $role) {
            $role->permissions()->detach($permission->id);
        }
    }
}
