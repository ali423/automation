<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UserRestorePasswordRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Jobs\NotifyAdminsJob;
use App\Models\Commodity;
use App\Models\Role;
use App\Models\User;
use App\Notifications\CommodityWarningNotification;
use App\Services\UserService;

class UserController extends Controller
{
    protected $service;
    public function __construct(UserService $service)
    {
        $this->service=$service;
        $this->authorizeResource(User::class);
        $this->shareView();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $users=User::query()
            ->with('activities')
            ->orderBy('id', 'DESC')->get();
        return view('dashboard.user.index',
            [
                'users'=>$users,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $roles=Role::all();
        return view('dashboard.user.create',[
            'roles'=>$roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(UserRequest $request)
    {
        $this->service->create($request->validationData());
        return redirect(route('user.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('dashboard.user.show',
            [
                'user'=>$user,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('dashboard.user.edit',
            [
                'user'=>$user,
                'roles'=>Role::all(),
            ]);
    }

    public function resetPassword(User $user){
        return view('dashboard.user.restore-password',
            [
                'user'=>$user,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $this->service->update($user,$request->validationData());
        return redirect(route('user.show',$user))->with('successful', 'اطلاعات ویرایش شد.');
    }

    public function resetPasswordStore(UserRestorePasswordRequest $request, User $user)
    {
        $this->service->restorePassword($user,$request->validationData());
        return redirect(route('user.show',$user))->with('successful', 'کلمه عبور ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
