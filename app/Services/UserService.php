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
        $warning_message=false;
        if ($data['warning_message'] ?? null == 'on'){
            $warning_message=true;
        }
        return User::query()->create([
            'role_id' => $data['role'],
            'user_name' => $data['user_name'],
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'status' => $data['status'],
            'password' => bcrypt($data['password']),
            'mobile'=>$data['mobile'] ?? null ,
            'warning_message'=>$warning_message ,
        ]);
    }

    public function update(User $user,$data){
        $warning_message=false;
        if ($data['warning_message'] ?? null == 'on'){
            $warning_message=true;
        }
        return $user->update([
            'role_id' => $data['role'],
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'status' => $data['status'],
            'mobile'=>$data['mobile'] ?? null ,
            'warning_message'=>$warning_message ,
        ]);
    }
    public function restorePassword(User $user,$data){
        return $user->update([
            'password' => bcrypt($data['password']),
        ]);
    }
}
