<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function __construct()
    {
        //
    }

    public function create($data)
    {
        return User::query()->create([
            'role_id' => $data['role'],
            'user_name' => $data['user_name'],
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'status' => $data['status'],
            'password' => $data['password'],
        ]);
    }

    public function update(User $user,$data){
        return $user->update([
            'role_id' => $data['role'],
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'status' => $data['status'],
        ]);
    }
    public function restorePassword(User $user,$data){
        return $user->update([
            'password' => bcrypt($data['password']),
        ]);
    }
}
