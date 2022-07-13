@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')
    <!-- These plugins only need for the run this page -->
    <link rel="stylesheet" href="{{ asset('js/default-assets/vector-map/jquery-jvectormap-2.0.2.css') }}">
@endsection

@section('content')

    <!-- Main Content Area -->
    <div id="main-links" class="row">
    @if(Gate::check('read_user') || Gate::check('create_user'))
        <!-- Single Widget -->
            <div class="main-link col-12 col-3 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="user">
                        <div class="text-center">
                            <div>
                                <i class="ti-user font-24"></i>
                            </div>
                            <h6>کاربران</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست کاربران</a></li>
                                <li><a href="#" class="btn btn-white m-1">افزودن کاربر جدید</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    @if(Gate::check('read_role') || Gate::check('create_role'))
        <!-- Single Widget -->
            <div class="col-12 col-3 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="role">
                        <div class="text-center">
                            <div>
                                <i class="ti-id-badge font-24"></i>
                            </div>
                            <h6>نقش ها</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست نقش ها</a></li>
                                <li><a href="#" class="btn btn-white m-1">افزودن نقش جدید</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    @can('read_activity',App\Models\Activity::class)
        <!-- Single Widget -->
            <div class="col-12 col-3 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="activity">
                        <div class="text-center">
                            <div>
                                <i class="icon-search font-24"></i>
                            </div>
                            <h6>فعالیت ها</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست فعالیت ها</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    @if(Gate::check('read_commodity') || Gate::check('create_commodity'))
        <!-- Single Widget -->
            <div class="col-12 col-3 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="commodity">
                        <div class="text-center">
                            <div>
                                <i class="icon-layers font-24"></i>
                            </div>
                            <h6>کالاهای سیستم</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست کالا ها</a></li>
                                <li><a href="#" class="btn btn-white m-1">افزودن کالای جدید</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    @if(Gate::check('read_warehouse') || Gate::check('create_warehouse'))
        <!-- Single Widget -->
            <div class="col-12 col-3 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="warehouse">
                        <div class="text-center">
                            <div>
                                <i class="black-text ti-home font-24"></i>
                            </div>
                            <h6 class="black-text">انبارها</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex flex-wrap">
                                <li><a href="#" class="btn btn-white m-1">لیست انبارها</a></li>
                                <li><a href="#" class="btn btn-white m-1">افزودن انبار جدید</a></li>
                                <li><a href="#" class="btn btn-white m-1">وضعیت موجودی انبار</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    @if(Gate::check('read_importing') || Gate::check('create_importing'))
        <!-- Single Widget -->
            <div class="col-12 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="process">
                        <div class="text-center">
                            <div>
                                <i class="ti-truck font-24"></i>
                            </div>
                            <h6>ورود کالا به انبار</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست درخواست ها</a></li>
                                <li><a href="#" class="btn btn-white m-1">ثبت درخواست</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    @if(Gate::check('read_customer') || Gate::check('create_customer'))
        <!-- Single Widget -->
            <div class="col-12 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="customer">
                        <div class="text-center">
                            <div>
                                <i class="ti-shopping-cart font-24"></i>
                            </div>
                            <h6>مشتری ها</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست مشتریان</a></li>
                                <li><a href="#" class="btn btn-white m-1">ثبت مشتری</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    @if(Gate::check('read_withdrawal') || Gate::check('create_withdrawal'))
        <!-- Single Widget -->
            <div class="col-12 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="withrawal">
                        <div class="text-center">
                            <div>
                                <i class="ti-shopping-cart-full font-24"></i>
                            </div>
                            <h6>فروش فرآورده</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست فروش</a></li>
                                <li><a href="#" class="btn btn-white m-1">ثبت فروش</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
                @endif

        </div>
        <!-- Single Widget -->
        <div class="col-12 col-md box-margin height-card">
            <div class="card">
                <div class="link card-body d-flex align-items-center justify-content-center" data-link="order">
                    <div class="text-center">
                        <div>
                            <i class="ti-receipt font-24"></i>
                        </div>
                        <h6>سفارشات</h6>
                    </div>
                    <div class="d-md-none">
                        <ul class="list-unstyled d-flex">
                            <li><a href="#" class="btn btn-white m-1">لیست فروش</a></li>
                            <li><a href="#" class="btn btn-white m-1">ثبت فروش</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="users-links" class="row d-none d-md-flex">
        <div class="col box-margin height-card">
            <div class="card">
                {{-- start user --}}
                <div id="user" class="card-body row">
                    <!-- Single Widget -->
                    @can('read_user',App\Models\User::class)
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('user.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست کاربران</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('create_user',App\Models\User::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('user.create') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>افزودن کاربر جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                {{-- end user --}}

                {{-- start role --}}
                <div id="role" class="d-none card-body row">
                    <!-- Single Widget -->
                    @can('read_role',App\Models\Role::class)
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('role.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست نقش ها</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('create_role',App\Models\Role::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('role.create') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>افزودن نقش جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                {{-- end role --}}
                @can('read_activity',App\Models\Activity::class)
                    {{-- start activity --}}
                    <div id="activity" class="d-none card-body row">
                        <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('activity.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست فعالیت ها</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end activity --}}
                @endcan
                {{-- start commodity --}}
                <div id="commodity" class="d-none card-body row">
                    <!-- Single Widget -->
                    @can('read_commodity',App\Models\Commodity::class)
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('commodity.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست کالاها</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('create_commodity',App\Models\Commodity::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('commodity.create') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>افزودن کالای جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                {{-- end commodity --}}

                {{-- start warehouse --}}
                <div id="warehouse" class="d-none card-body row">
                    <!-- Single Widget -->
                    @can('read_warehouse',App\Models\Warehouse::class)
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('warehouse.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست انبار ها</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('create_warehouse',App\Models\Warehouse::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('warehouse.create') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>افزودن انبار جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                <!-- Single Widget -->
                    @can('read_warehouse',App\Models\Warehouse::class)
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('inventory.index') }}" class="bg-green">
                                        <div>
                                            <div>
                                                <i class="ti-bar-chart font-24"></i>
                                            </div>
                                            <h6>وضعیت موجودی انبار</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                {{-- end warehouse --}}
                {{-- start proccess --}}
                <div id="process" class="d-none card-body row">
                @can('read_importing',App\Models\ImportingRequest::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('importing-request.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست درخواست ها</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                @endif
                @can('create_importing',App\Models\ImportingRequest::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('importing-request.create') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>ثبت درخواست جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                {{-- end process --}}
                {{-- start customer --}}
                <div id="customer" class="d-none card-body row">
                @can('read_customer',App\Models\Customer::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('customer.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست مشتریان</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                @endcan
                @can('create_customer',App\Models\Customer::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('customer.create') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>ثبت مشتری جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                {{-- end customer --}}
                {{-- start customer --}}
                <div id="withrawal" class="d-none card-body row">
                    <!-- Single Widget -->
                    @can('read_withdrawal',App\Models\WithdrawalRequest::class)
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('withdrawal-request.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست فروش</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('create_withdrawal',App\Models\WithdrawalRequest::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('withdrawal-request.create') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>ثبت فروش جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                {{-- end customer --}}
                {{-- start order --}}
                <div id="order" class="d-none card-body row">
                    <!-- Single Widget -->
                    <div class="col height-card">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                </br></br>
                                <a href="{{ route('order.index') }}" class="bg-red">
                                    <div>
                                        <div>
                                            <i class="ti-list-ol font-24"></i>
                                        </div>
                                        <h6>لیست سفارشات</h6>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Single Widget -->
                    <div class="col height-card">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <a href="{{ route('order.create') }}" class="bg-blue">
                                    <div>
                                        <div>
                                            <i class="ti-write font-24"></i>
                                        </div>
                                        <h6>ثبت سفارش جدید</h6>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end order --}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 height-card box-margin">
            <div class="card">
                <div class="card-body">
                    <!-- Stacked Bar -->
                    <div id="panel-15" class="panel">
                        <h4 class="card-title">وضعیت انبار ها</h4>
                        <div class="row">
                            <div class="col-md-10">
                                <div id="mychart" class="row">
                                    {{-- @php($i = 1)
                                    @foreach ($warehouses as $warehouse)
                                        <div class="col mr-1 mt-5 mt-3">
                                            <span class="size">
                                                {{ number_format($warehouse->capacity) }} کیلوگرم
                                            </span>
                                            <div class="full-size">
                                                <span></span>
                                                <span
                                                    @switch($i) @case(1)
                                          class="current-size bg-primary"
                                        @break
                                        @case(2)
                                        class="current-size bg-success"
                                        @break
                                    @case(3)
                                    class="current-size bg-danger"
                                    @break
                                    @case(4)
                                    class="current-size bg-secondary"
                                    @break
                                    @case(5)
                                    class="current-size bg-warning"
                                    @php($i=0)
                                    @break @endswitch
                                                    data-current="{{ $warehouse->full_space_percentage }}"></span>
                                            </div>
                                            <span>
                                                {{ $warehouse->title }}
                                            </span>
                                        </div>
                                        @php($i++)
                                    @endforeach --}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <img src="{{ asset('img/ware-capacity/warechartex.png')}}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 height-card box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">وضعیت کالا ها</h4>
                    <div id="chartContainer" style="height: 300px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/apexchart.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dashboard-active.js') }}"></script>
    <script src="{{ asset('js/default-assets/peity.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/peity-demo.js') }}"></script>
    <script src="{{ asset('js/default-assets/vector-map/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/vector-map/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('js/default-assets/vector-map/jquery-jvectormap-in-mill.js') }}"></script>
    <script src="{{ asset('js/default-assets/vector-map/jquery-jvectormap-us-aea-en.js') }}"></script>
    <script src="{{ asset('js/default-assets/vector-map/jquery-jvectormap-uk-mill-en.js') }}"></script>
    <script src="{{ asset('js/default-assets/vector-map/jquery-jvectormap-au-mill.js') }}"></script>
    <script src="{{ asset('js/default-assets/vector-map/jvectormap.custom.js') }}"></script>
    <script src="{{ asset('js/canvas.min.js') }}"></script>

    {{-- main links js --}}
    <script src="{{ asset('js/main-links/main-links.js') }}"></script>

    {{-- commodity in ware chart --}}
    <script>
        window.onload = function () {
        
        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            exportEnabled: true,
            title:{
                //header
                text: "", 
                fontFamily: "yekan black",
                fontColor: "#695A42"
            },
            axisX: {
                // title: "کالاها"
            },
            axisY:{
                valueFormatString:"#0 kg",
                gridColor: "#B6B1A8",
                tickColor: "#B6B1A8"
            },
            toolTip: {
                shared: true,
                content: toolTipContent
            },
            data: [{
                    type: "stackedColumn",
                    // showInLegend: true,
                    color: "#00b6e4",
                    name: "انبار 1",
                    dataPoints: [
                        { y: 6.75,  label: "کالا 1"},
                        { y: 8.57,  label: "کالا 2"},
                        { y: 10.64,  label: "کالا 3"},
                        { y: 13.97,  label: "کالا 4"},
                        { y: 15.42,  label: "کالا 5"}
                    ]
                },
                {        
                    type: "stackedColumn",
                    // showInLegend: true,
                    name: "انبار 2",
                    color: "#00b6e4",
                    dataPoints: [
                        { y: 6.82,  label: "کالا 1"},
                        { y: 9.02,  label: "کالا 2"},
                        { y: 11.80,  label: "کالا 3"},
                        { y: 14.11,  label: "کالا 4"},
                        { y: 15.96,  label: "کالا 5"}
                    ]
                },
                {        
                    type: "stackedColumn",
                    // showInLegend: true,
                    name: "انبار 3",
                    color: "#00b6e4",
                    dataPoints: [
                        { y: 7.28,  label: "کالا 1" },
                        { y: 9.72,  label: "کالا 2" },
                        { y: 13.30,  label: "کالا 3"},
                        { y: 14.9,  label: "کالا 4"},
                        { y: 18.10,  label: "کالا 5"}
                    ]
                },
                {        
                    type: "stackedColumn",
                    // showInLegend: true,
                    name: "انبار 4",
                    color: "#00b6e4",
                    dataPoints: [
                        { y: 8.44,  label: "کالا 1" },
                        { y: 10.58,  label: "کالا 2" },
                        { y: 14.41,  label: "کالا 3"},
                        { y: 16.86,  label: "کالا 4"},
                        { y: 10.64,  label: "کالا 5" }
                    ]
                }]
        });
        chart.render();
        
        function toolTipContent(e) {
            var str = "";
            var total = 0;
            var str2, str3;
            for (var i = 0; i < e.entries.length; i++){
                var  str1 = "<span style= \"color:"+e.entries[i].dataSeries.color + "\"> "+e.entries[i].dataSeries.name+"</span> : <strong>"+e.entries[i].dataPoint.y+" کیلوگرم</strong><br/>";
                total = e.entries[i].dataPoint.y + total;
                str = str.concat(str1);
            }
            str2 = `<b>${e.entries[0].dataPoint.label}</b><br/>`;
            total = Math.round(total * 100) / 100;
            str3 = "<span style = \"color:Tomato\">مجموع : </span><strong>"+total+" کیلوگرم</strong><br/>";
            return (str2.concat(str)).concat(str3);
        }
        
        }
        </script>

@endsection
