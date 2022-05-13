<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $role=Role::query()->paginate(20);
        return view('dashboard.role.index',
            [
                'roles'=>$role,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.role.create',
        [
            'permissions'=>Permission::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(RoleRequest $request)
    {
        $data=$request->validationData();
        $role=Role::query()->create([
            'name'=>$data['name'],
            'title'=>$data['title'],
        ]);
        $role->permissions()->attach($data['permissions']);
        return redirect(route('role.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return view('dashboard.role.show',
            [
                'role'=>$role,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('dashboard.role.edit',
            [
                'role'=>$role,
                'permissions'=>Permission::all(),
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $data=$request->validationData();
        $role->update([
            'name'=>$data['name'],
            'title'=>$data['title'],
        ]);
        $role->permissions()->sync($data['permissions']);
        return redirect(route('role.index'))->with('successful', 'ویرایش انجام شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        if ($role->users()->exists()){
            return redirect()->back()->withErrors('ابتدا کاربرانی که این نقش را دارند اصلاح کنید');
        }
        $role->Permissions()->detach();
        $role->delete();
        return redirect(route('role.index'))->with('successful', 'حذف اطلاعات انجام شد.');

    }
}
