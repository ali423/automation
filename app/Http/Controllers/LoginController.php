<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
   public function create(){
       return view('login.index');
   }

   public function store(LoginRequest $request){

       $data=$request->validationData();
       $user = User::query()->where('user_name', $data['user_name'])->first();
       if (Hash::check($request->get('password'), $user->password)) {
               auth()->login($user);
               return redirect(route('home'));
           }
       return redirect()->back()->withErrors('رمز عبور صحیح نیست');
   }

   public function logout(){
       auth()->logout();
       return redirect(route('login'));
   }
}
