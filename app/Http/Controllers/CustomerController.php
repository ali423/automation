<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use PaginationTrait;
    
    protected $service;

    public function __construct(CustomerService $service)
    {
        $this->service=$service;
        $this->authorizeResource(Customer::class);
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
        $query = Customer::with(['activities']);
        
        // Use advanced pagination with search and filter capabilities
        $customers = $this->getPaginatedResults($query, $request, 10, [
            'searchable_fields' => ['name', 'mobile', 'comp_name', 'national_code', 'economic_code'],
            'filterable_fields' => [],
            'sortable_fields' => ['id', 'name', 'mobile', 'comp_name', 'created_at', 'updated_at'],
            'default_sort_field' => 'created_at',
            'default_sort_direction' => 'desc',
            'max_per_page' => 50
        ]);
        
        // Prepare options for the pagination components
        $paginationOptions = [
            'searchable_fields' => ['name', 'mobile', 'comp_name', 'national_code', 'economic_code'],
            'filterable_fields' => [],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در نام، موبایل، نام شرکت، کد ملی یا کد اقتصادی...'
        ];
        
        return view('dashboard.customer.index', [
            'customers' => $customers,
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
        return view('dashboard.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CustomerRequest $request)
    {
        $data=$request->validated();
        $this->service->create($data);
        return redirect(route('customer.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return view('dashboard.customer.show',[
            'customer'=>$customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('dashboard.customer.edit',[
            'customer'=>$customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        $this->service->update($customer,$request->validated());
        return redirect(route('customer.show',$customer))->with('successful', 'اطلاعات ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect(route('customer.index'))->with('successful', 'اطلاعات حذف شدند.');
    }
}
