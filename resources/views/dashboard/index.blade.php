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
    @if(Gate::check('read_unit') || Gate::check('create_unit'))
        <!-- Single Widget -->
            <div class="col-12 col-3 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="unit">
                        <div class="text-center">
                            <div>
                                <i class="ti-ruler-pencil font-24"></i>
                            </div>
                            <h6>واحدهای اندازه‌گیری</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست واحد ها</a></li>
                                <li><a href="#" class="btn btn-white m-1">افزودن واحد جدید</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    @endif
    @if(Gate::check('read_unit_conversion') || Gate::check('create_unit_conversion'))
        <!-- Single Widget -->
            <div class="col-12 col-3 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="unit-conversion">
                        <div class="text-center">
                            <div>
                                <i class="ti-exchange-vertical font-24"></i>
                            </div>
                            <h6>تبدیل واحد ها</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست تبدیل ها</a></li>
                                <li><a href="#" class="btn btn-white m-1">افزودن تبدیل جدید</a></li>
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
                                                            <h6>خرید کالا</h6>
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
    @if(Gate::check('read_production') || Gate::check('create_production'))
        <!-- Single Widget -->
            <div class="col-12 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="production">
                        <div class="text-center">
                            <div>
                                <i class="ti-settings font-24"></i>
                            </div>
                            <h6>تولید</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">لیست درخواست های تولید</a></li>
                                <li><a href="#" class="btn btn-white m-1">ثبت درخواست تولید</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if(Gate::check('read_order') || Gate::check('create_order'))
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
        @endif

    @if(Gate::check('read_order') || Gate::check('create_order'))
        <!-- Single Widget -->
            <div class="col-12 col-md box-margin height-card">
                <div class="card">
                    <div class="link card-body d-flex align-items-center justify-content-center" data-link="factory-status">
                        <div class="text-center">
                            <div>
                                <i class="ti-bar-chart font-24"></i>
                            </div>
                            <h6>وضعیت کارخونه</h6>
                        </div>
                        <div class="d-md-none">
                            <ul class="list-unstyled d-flex">
                                <li><a href="#" class="btn btn-white m-1">مشاهده وضعیت</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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

                {{-- start unit --}}
                <div id="unit" class="d-none card-body row">
                    <!-- Single Widget -->
                    @can('read_unit',App\Models\Unit::class)
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('unit.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست واحدها</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('create_unit',App\Models\Unit::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('unit.create') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>افزودن واحد جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                {{-- end unit --}}

                {{-- start unit-conversion --}}
                <div id="unit-conversion" class="d-none card-body row">
                    <!-- Single Widget -->
                    @can('read_unit_conversion',App\Models\UnitConversion::class)
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('unit-conversion.select-commodity') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست تبدیل ها</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @can('create_unit_conversion',App\Models\UnitConversion::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('unit-conversion.select-commodity') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>افزودن تبدیل جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                {{-- end unit-conversion --}}

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
                {{-- start production --}}
                <div id="production" class="d-none card-body row">
                @can('read_production',App\Models\ProductionRequest::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('production-request.index') }}" class="bg-red">
                                        <div>
                                            <div>
                                                <i class="ti-list-ol font-24"></i>
                                            </div>
                                            <h6>لیست درخواست های تولید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                @endcan
                @can('create_production',App\Models\ProductionRequest::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <a href="{{ route('production-request.create') }}" class="bg-blue">
                                        <div>
                                            <div>
                                                <i class="ti-write font-24"></i>
                                            </div>
                                            <h6>ثبت درخواست تولید جدید</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
                {{-- end production --}}
                {{-- start order --}}
                <div id="order" class="d-none card-body row">
                @can('read_order',App\Models\Order::class)
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
                @endcan
                @can('create_order',App\Models\Order::class)
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
                    @endcan
                </div>
                {{-- end order --}}

                {{-- start factory-status --}}
                <div id="factory-status" class="d-none card-body row">
                @can('read_order',App\Models\Order::class)
                    <!-- Single Widget -->
                        <div class="col height-card">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    </br></br>
                                    <a href="{{ route('order.factory-status') }}" class="bg-green">
                                        <div>
                                            <div>
                                                <i class="ti-factory font-24"></i>
                                            </div>
                                            <h6>مشاهده وضعیت کارخونه</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                @endcan
                </div>
                {{-- end factory-status --}}
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


@endsection
