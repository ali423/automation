<?php

namespace App\Http\Controllers\Processes;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWithdrawalRequest;
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateWithdrawalRequest $request)
    {
        $data = $request->only('commodity_id', 'warehouse_id', 'unit', 'amount', 'comment','price','customer_id');
        $inventory_check = $this->service->checkInventory($data);
        if ($inventory_check['success'] == true) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
            }
            $this->service->create($data, $file ?? null);
        } else {
            return redirect()->back()->withErrors($inventory_check['error']);
        }
        return redirect(route('importing-request.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(WithdrawalRequest $withdrawalRequest)
    {
        return view('dashboard.processes.withdrawal-request.show', [
            'request' => $withdrawalRequest,
            'warehouses' => Warehouse::all(),
        ]);
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

    public function approvalRequest($id)
    {
        $withdrawal_request = WithdrawalRequest::query()->findOrFail($id);
        if ($withdrawal_request->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان تایید وجود ندارد .');
        }
        $check_expired=$this->service->checkExpiredRequest($withdrawal_request);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        $check_warehouses = $this->service->checkInventoryApproval($withdrawal_request);
        if ($check_warehouses['success'] == true) {
            $this->service->approvalRequest($withdrawal_request);
        } else {
            return redirect()->back()->withErrors($check_warehouses['error']);
        }
        return redirect(route('withdrawal-request.show', $withdrawal_request))->with('successful', 'درخواست با موفقیت تایید شد.');
    }
    public function rejectRequest($id){
        $withdrawal_request = WithdrawalRequest::query()->findOrFail($id);
        if ($withdrawal_request->status != 'awaiting_approval') {
            return redirect()->back()->withErrors('در این مرحله امکان رد وجود ندارد .');
        }
        $check_expired=$this->service->checkExpiredRequest($withdrawal_request);
        if ($check_expired['success'] == false) {
            return redirect()->back()->withErrors($check_expired['error']);
        }
        $this->service->rejectRequest($withdrawal_request);
        return redirect(route('withdrawal-request.show', $withdrawal_request))->with('successful', 'درخواست با موفقیت رد شد.');
    }
}
