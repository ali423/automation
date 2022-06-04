<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommodityRequest;
use App\Http\Requests\CommodityUpdateRequest;
use App\Models\Commodity;
use App\Services\CommodityService;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    protected $service;

    public function __construct(CommodityService $service)
    {
        $this->service=$service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $commodities=Commodity::query()->orderBy('id', 'DESC')->get();
        return view('dashboard.commodity.index',
            [
                'commodities'=>$commodities,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.commodity.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CommodityRequest $request)
    {
        $this->service->create($request->validationData());
        return redirect(route('commodity.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Commodity  $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Commodity $commodity)
    {
        return view('dashboard.commodity.show',[
            'commodity'=>$commodity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Commodity  $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Commodity $commodity)
    {
        return view('dashboard.commodity.edit',[
            'commodity'=>$commodity,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Commodity  $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(CommodityUpdateRequest $request, Commodity $commodity)
    {
        $this->service->update($commodity,$request->validationData());
        return redirect(route('commodity.show',$commodity))->with('successful', 'اطلاعات ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Commodity  $commodity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Commodity $commodity)
    {
        $commodity->delete();
        return redirect(route('commodity.index'))->with('successful', 'اطلاعات حذف شدند.');

    }
}