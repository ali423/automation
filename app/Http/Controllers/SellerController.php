<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerRequest;
use App\Http\Requests\SellerUpdateRequest;
use App\Models\Seller;
use App\Services\SellerService;
use Illuminate\Http\Request;

class SellerController extends Controller
{
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $sellers=Seller::query()
            ->with('activities')
            ->orderBy('id', 'DESC')->get();
        return view('dashboard.seller.index',
            [
                'sellers'=>$sellers,
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
