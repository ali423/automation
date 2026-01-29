<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_role = Role::query()->firstOrCreate(
            ['name' => 'مدیر'],
            ['title' => 'admin']
        );

        $admin_role->update(['title' => 'admin']);
        $admin_role->Permissions()->syncWithoutDetaching(Permission::pluck('id'));

        User::query()->firstOrCreate(
            ['user_name' => 'admin'],
            [
                'role_id' => $admin_role->id,
                'name' => 'نوید',
                'status' => 'active',
                'lastname' => 'طهماسبی',
                'email' => 'ali_mokhtari72@yahoo.com',
                'password' => bcrypt('a13760406'),
                'mobile'=>'09121307723',
                'warning_message'=>true,
            ]
        );
    }
}
