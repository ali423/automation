<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWithdrawalRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Commodity;
use App\Models\Customer;
use App\Models\Order;
use App\Models\WithdrawalRequest;
use App\Services\OrderService;
use App\Services\Processes\WithdrawalRequestService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $service;
    protected $withdrawal_service;

    public function __construct(OrderService $service,WithdrawalRequestService $withdrawal_service)
    {
        $this->service = $service;
        $this->withdrawal_service=$withdrawal_service;
        $this->authorizeResource(Order::class);
        $this->shareView();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::query()->with(['customer', 'commodity','activities'])
            ->orderByRaw("FIELD(status, \"pending\", \"done\")")
            ->orderBy('deadline', 'ASC')->get();
        return view('dashboard.order.index',
            [
                'orders' => $orders,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $commodities = Commodity::query()->where('type', 'product')->get();
        $customers = Customer::query()->get();
        if (count($commodities) < 1) {
            return redirect(route('commodity.create'))->withErrors('ابتدا حداقل یک کالای فراورده ثبت کنید .');
        }
        if (count($customers) < 1) {
            return redirect(route('customer.create'))->withErrors('ابتدا حداقل یک مشتری ثبت کنید .');
        }
        return view('dashboard.order.create',
            [
                'commodities' => $commodities,
                'customers' => $customers,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(OrderRequest $request)
    {
        $data = $request->only(['customer_id', 'commodity_id', 'unit', 'deadline', 'price', 'commodity_amount']);
        $this->service->create($data);
        return redirect(route('order.index'))->with('successful', 'اطلاعات ثبت شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return view('dashboard.order.show',
            [
                'order' => $order,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Order $order)
    {
        if ($order->status != 'pending') {
            return redirect()->back()->withErrors('امکان ویرایش سفارش تحویل شده وجود ندارد');
        }
        $commodities = Commodity::query()->where('type', 'product')->get();
        $customers = Customer::query()->get();
        return view('dashboard.order.edit',
            [
                'order' => $order,
                'commodities' => $commodities,
                'customers' => $customers,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(OrderRequest $request, Order $order)
    {
        if ($order->status != 'pending') {
            return redirect()->back()->withErrors('امکان ویرایش سفارش تحویل شده وجود ندارد');
        }
        $data = $request->only(['customer_id', 'commodity_id', 'unit', 'deadline', 'price', 'commodity_amount']);
        $this->service->update($order, $data);
        return redirect(route('order.show', $order))->with('successful', 'اطلاعات ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Order $order)
    {
        if ($order->status != 'pending') {
            return redirect()->back()->withErrors('امکان حذف سفارش تحویل شده وجود ندارد');
        }
        $order->delete();
        return redirect(route('order.index'))->with('successful', 'سفارش حذف شد.');
    }

    public function confirm(Order $order){
        return view('dashboard.order.confirm',
            [
                'order' => $order,
            ]);
    }

    public function confirmStore(CreateWithdrawalRequest $request,Order $order){
        $data = $request->only('commodity_id', 'warehouse_id', 'unit', 'amount', 'comment','price','customer_id');
        $inventory_check = $this->withdrawal_service->checkInventory($data);
        if ($inventory_check['success'] == true) {
            $file=null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
            }
            $withdrawal = DB::transaction(function () use ($order,$data, $file) {
                $this->service->updateStatus($order);
                return $this->withdrawal_service->create($data, $file );
            });
        } else {
            return redirect()->back()->withErrors($inventory_check['error']);
        }
        return redirect(route('withdrawal-request.show',$withdrawal))->with('successful', 'اطلاعات ثبت شد.');
    }
}
