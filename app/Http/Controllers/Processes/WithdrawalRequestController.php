<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\WithdrawalRequest;
use App\Services\Processes\WithdrawalRequestService;
use Illuminate\Http\Request;

class WithdrawalRequestController extends Controller
{
    protected $service;

    public function __construct(WithdrawalRequestService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $requests = WithdrawalRequest::query()->orderBy('id', 'DESC')->get();
        return view('dashboard.processes.withdrawal-request.index',
            [
                'requests' => $requests,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.processes.withdrawal-request.create', [
            'commodities' => Commodity::query()->get(),
            'customers' => Customer::query()->get(),
            'warehouses' => Warehouse::query()->where('status', 'active')->get(),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Http\Response
     */
    public function show(WithdrawalRequest $withdrawalRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(WithdrawalRequest $withdrawalRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(WithdrawalRequest $withdrawalRequest)
    {
        //
    }
}
