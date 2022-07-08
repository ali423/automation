@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')
    <!-- These plugins only need for the run this page -->
    <link rel="stylesheet" href="{{ asset('js/default-assets/vector-map/jquery-jvectormap-2.0.2.css') }}">
@endsection

@section('content')

    <!-- Main Content Area -->
    <div id="main-links" class="row">
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

        <!-- Single Widget -->
        <div class="col-12 col-md box-margin height-card">
            <div class="card">
                <div class="link card-body d-flex align-items-center justify-content-center" data-link="process">
                    <div class="text-center">
                        <div>
                            <i class="ti-truck font-24"></i>
                        </div>
                        <h6>انتقالات انبار</h6>
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
    </div>

    <div id="users-links" class="row d-none d-md-flex">
        <div class="col box-margin height-card">
            <div class="card">
                {{-- start user --}}
                <div id="user" class="card-body row">
                    <!-- Single Widget -->
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
                </div>
                {{-- end user --}}

                {{-- start role --}}
                <div id="role" class="d-none card-body row">
                    <!-- Single Widget -->
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
                </div>
                {{-- end role --}}

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

                {{-- start commodity --}}
                <div id="commodity" class="d-none card-body row">
                    <!-- Single Widget -->
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
                </div>
                {{-- end commodity --}}

                {{-- start warehouse --}}
                <div id="warehouse" class="d-none card-body row">
                    <!-- Single Widget -->
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

                    <!-- Single Widget -->
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
                </div>
                {{-- end warehouse --}}
                {{-- start proccess --}}
                <div id="process" class="d-none card-body row">
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
                </div>
                {{-- end process --}}
                {{-- start customer --}}
                <div id="customer" class="d-none card-body row">
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
                </div>
                {{-- end customer --}}
                {{-- start customer --}}
                <div id="withrawal" class="d-none card-body row">
                    <!-- Single Widget -->
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
                </div>
                {{-- end customer --}}
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

    {{-- main links js --}}
    <script src="{{ asset('js/main-links/main-links.js') }}"></script>
@endsection
