<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerRequest;
use App\Http\Requests\SellerUpdateRequest;
use App\Models\Seller;
use App\Services\SellerService;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    use PaginationTrait;
    
    protected $service;

    public function __construct(SellerService $service)
    {
        $this->service=$service;
        $this->authorizeResource(Seller::class);
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
        $query = Seller::with(['activities']);
        
        // Use advanced pagination with search and filter capabilities
        $sellers = $this->getPaginatedResults($query, $request, 10, [
            'searchable_fields' => ['name', 'mobile', 'comp_name', 'national_code', 'economic_code'],
            'filterable_fields' => [],
            'sortable_fields' => ['id', 'name', 'mobile', 'comp_name', 'created_at', 'updated_at'],
            'default_sort_field' => 'created_at',
            'default_sort_direction' => 'desc',
            'max_per_page' => 100
        ]);
        
        // Prepare options for the pagination components
        $paginationOptions = [
            'searchable_fields' => ['name', 'mobile', 'comp_name', 'national_code', 'economic_code'],
            'filterable_fields' => [],
            'per_page_options' => [5, 10, 25, 50, 100],
            'search_placeholder' => 'جستجو در نام، موبایل، نام شرکت، کد ملی یا کد اقتصادی...'
        ];
        
        return view('dashboard.seller.index', [
            'sellers' => $sellers,
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
        return view('dashboard.seller.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(SellerRequest $request)
    {
        $data=$request->validated();
        $this->service->create($data);
        return redirect(route('seller.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
        return view('dashboard.seller.show',[
            'seller'=>$seller,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Seller $seller)
    {
        return view('dashboard.seller.edit',[
            'seller'=>$seller,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(SellerUpdateRequest $request, Seller $seller)
    {
        $this->service->update($seller,$request->validated());
        return redirect(route('seller.show',$seller))->with('successful', 'اطلاعات ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Seller $seller)
    {
        $seller->delete();
        return redirect(route('seller.index'))->with('successful', 'اطلاعات حذف شدند.');
    }
}
