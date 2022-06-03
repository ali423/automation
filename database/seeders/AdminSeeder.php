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
        $admin_role = Role::query()
            ->create([
                'name' => 'مدیر',
                'title' => 'admin',
            ]);
        $admin_role->Permissions()->attach(Permission::all());

        User::query()
            ->create([
                'role_id' => $admin_role->id,
                'name' => 'نوید',
                'user_name' => 'admin',
                'status' => 'active',
                'lastname' => 'طهماسبی',
                'email' => 'ali_mokhtari72@yahoo.com',
                'password' => bcrypt('a13760406'),
            ]);
    }
}
