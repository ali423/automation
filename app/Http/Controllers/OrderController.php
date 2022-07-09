<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Commodity;
use App\Models\Customer;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $service;

    public function __construct(OrderService $service)
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
        $orders = Order::query()->with(['customer', 'commodity'])->orderByRaw("FIELD(status, \"pending\", \"done\")")->orderBy('deadline', 'ASC')->get();
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
}
