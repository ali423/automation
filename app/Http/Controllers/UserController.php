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
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use PaginationTrait;
    
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
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Build query with eager loading to fix N+1 query problem
        $query = User::with(['role', 'activities']);
        
        // Use advanced pagination with search and filter capabilities
        $users = $this->getPaginatedResults($query, $request, 10, [
            'searchable_fields' => ['name', 'lastname', 'user_name', 'role.name'],
            'filterable_fields' => ['role_id'],
            'sortable_fields' => ['id', 'name', 'lastname', 'user_name', 'created_at', 'updated_at'],
            'default_sort_field' => 'created_at',
            'default_sort_direction' => 'desc',
            'max_per_page' => 100
        ]);
        
        // Prepare options for the pagination components
        $paginationOptions = [
            'searchable_fields' => ['name', 'lastname', 'user_name', 'role.name'],
            'filterable_fields' => ['role_id'],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در نام، نام خانوادگی، نام کاربری یا نقش...'
        ];
        
        // Get roles for filter dropdown
        $roles = Role::all();
        
        return view('dashboard.user.index', [
            'users' => $users,
            'roles' => $roles,
            'options' => $paginationOptions,
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        // User deletion logic can be implemented here if needed
        return response()->json(['message' => 'User deletion not implemented'], 501);
    }
}
