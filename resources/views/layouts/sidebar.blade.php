<!-- Side Nav -->
<div class="ecaps-sidenav" id="ecapsSideNav">
    <!-- Side Menu Area -->
    <div class="side-menu-area">
        <!-- Sidebar Menu -->
        <nav>
            <ul class="sidebar-menu" data-widget="tree">
                <li>
                    <a href="{{ route('home') }}" class='btn btn-dfprimary text-light'><i class="fa fa-home text-light"></i><span>صفحه اصلی</span></a>
                </li>
                @if(Gate::check('read_user') || Gate::check('create_user'))
                    <li
                        @if($first_url_part== 'user')
                        class="treeview active"
                        @else
                        class="treeview"
                        @endif>
                        <a href="javascript:void(0)"><i class="icon-profile-male"></i> <span>کاربران</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_user',App\Models\User::class)
                                <li @if($first_url_part== 'user' && $second_url_part== 'index') class="active" @endif><a href="{{ route('user.index') }}">لیست کاربران</a></li>
                            @endcan
                            @can('create_user',App\Models\User::class)
                                <li @if($first_url_part== 'user' && $second_url_part== 'create') class="active" @endif><a href="{{ route('user.create') }}">افزودن کاربر جدید</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @if(Gate::check('read_role') || Gate::check('create_role'))
                        <li
                            @if($first_url_part== 'role')
                            class="treeview active"
                            @else
                            class="treeview"
                            @endif>                        <a href="javascript:void(0)"><i class="icon_id"></i> <span>نقش ها</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_role',App\Models\Role::class)
                                <li @if($first_url_part== 'role' && $second_url_part== 'index') class="active" @endif><a href="{{ route('role.index') }}">لیست نقش ها</a></li>
                            @endcan
                            @can('create_role',App\Models\Role::class)
                                <li @if($first_url_part== 'role' && $second_url_part== 'create') class="active" @endif><a href="{{ route('role.create') }}">افزودن نقش جدید</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @can('read_activity',App\Models\Activity::class)
                        <li
                            @if($first_url_part== 'activity')
                            class="treeview active"
                            @else
                            class="treeview"
                            @endif>                        <a href="javascript:void(0)"><i class="icon-search"></i> <span>فعالیت ها</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            <li @if($first_url_part== 'activity' && $second_url_part== 'index') class="active" @endif><a href="{{ route('activity.index') }}">لیست فعالیت ها</a></li>
                        </ul>
                    </li>
                @endcan
                @if(Gate::check('read_commodity') || Gate::check('create_commodity'))
                        <li
                            @if($first_url_part== 'commodity')
                            class="treeview active"
                            @else
                            class="treeview"
                            @endif>                        <a href="javascript:void(0)"><i class="icon-layers"></i> <span>کالا های سیستم</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_commodity',App\Models\Commodity::class)
                                <li @if($first_url_part== 'commodity' && $second_url_part== 'index') class="active" @endif><a href="{{ route('commodity.index') }}">لیست کالا ها</a></li>
                            @endcan
                            @can('create_commodity',App\Models\Commodity::class)
                                <li @if($first_url_part== 'commodity' && $second_url_part== 'create') class="active" @endif><a href="{{ route('commodity.create') }}">افزودن کالا جدید</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @if(Gate::check('read_unit') || Gate::check('create_unit'))
                    <li
                        @if($first_url_part == 'unit')
                        class="treeview active"
                        @else
                        class="treeview"
                        @endif>
                        <a href="javascript:void(0)"><i class="ti-ruler-pencil"></i> <span>واحدهای اندازه‌گیری</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_unit', App\Models\Unit::class)
                                <li @if($first_url_part == 'unit' && $second_url_part == 'index') class="active" @endif>
                                    <a href="{{ route('unit.index') }}">لیست واحدها</a>
                                </li>
                            @endcan
                            @can('create_unit', App\Models\Unit::class)
                                <li @if($first_url_part == 'unit' && $second_url_part == 'create') class="active" @endif>
                                    <a href="{{ route('unit.create') }}">افزودن واحد جدید</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @if(Gate::check('read_warehouse') || Gate::check('create_warehouse'))
                        <li
                            @if($first_url_part== 'warehouse' || $first_url_part== 'inventory')
                            class="treeview active"
                            @else
                            class="treeview"
                            @endif>                        <a href="javascript:void(0)"><i class="ti-home"></i> <span>انبار ها</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_warehouse',App\Models\Warehouse::class)
                                <li @if($first_url_part== 'warehouse' && $second_url_part== 'index') class="active" @endif><a href="{{ route('warehouse.index') }}">لیست انبار ها</a></li>
                            @endcan
                            @can('create_warehouse',App\Models\Warehouse::class)
                                <li @if($first_url_part== 'warehouse' && $second_url_part== 'create') class="active" @endif><a href="{{ route('warehouse.create') }}">افزودن انبار جدید</a></li>
                            @endcan
                            @can('read_warehouse',App\Models\Warehouse::class)
                                <li @if($first_url_part== 'inventory' && $second_url_part== 'index') class="active" @endif><a href="{{ route('inventory.index') }}">وضعیت موجودی انبار</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @if(Gate::check('read_importing') || Gate::check('create_importing'))
                        <li
                            @if($first_url_part== 'importing-request')
                            class="treeview active"
                            @else
                            class="treeview"
                            @endif>                        <a href="javascript:void(0)"><i class="ti-truck"></i> <span>ورود کالا به انبار</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_importing',App\Models\ImportingRequest::class)
                                <li @if($first_url_part== 'importing-request' && $second_url_part== 'index') class="active" @endif><a href="{{ route('importing-request.index') }}">لیست درخواست ها</a></li>
                            @endcan
                            @can('create_importing',App\Models\ImportingRequest::class)
                                <li @if($first_url_part== 'importing-request' && $second_url_part== 'create') class="active" @endif><a href="{{ route('importing-request.create') }}">ثبت درخواست</a></li>
                            @endcan
                                @can('read_importing',App\Models\ImportingRequest::class)
                                    <li @if($first_url_part== 'importing' && $second_url_part== 'report') class="active" @endif><a href="{{ route('importing.report.create') }}">گزارش خرید کالا</a></li>
                                @endcan
                        </ul>
                    </li>
                @endif
                @if(Gate::check('read_customer') || Gate::check('create_customer'))
                        <li
                            @if($first_url_part== 'customer')
                            class="treeview active"
                            @else
                            class="treeview"
                            @endif>                        <a href="javascript:void(0)"><i class="ti-shopping-cart"></i> <span>مشتری ها</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_customer',App\Models\Customer::class)
                                <li @if($first_url_part== 'customer' && $second_url_part== 'index') class="active" @endif><a href="{{ route('customer.index') }}">لیست مشتریان</a></li>
                            @endcan
                            @can('create_customer',App\Models\Customer::class)
                                <li @if($first_url_part== 'customer' && $second_url_part== 'create') class="active" @endif><a href="{{ route('customer.create') }}">ثبت مشتری</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif

                @if(Gate::check('read_seller') || Gate::check('create_seller'))
                    <li
                        @if($first_url_part== 'seller')
                        class="treeview active"
                        @else
                        class="treeview"
                        @endif>                        <a href="javascript:void(0)"><i class="ti-shopping-cart"></i> <span>فروشنده ها</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_seller',App\Models\seller::class)
                                <li @if($first_url_part== 'seller' && $second_url_part== 'index') class="active" @endif><a href="{{ route('seller.index') }}">لیست فروشنده ها</a></li>
                            @endcan
                            @can('create_seller',App\Models\seller::class)
                                <li @if($first_url_part== 'seller' && $second_url_part== 'create') class="active" @endif><a href="{{ route('seller.create') }}">ثبت فروشنده</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif

                @if(Gate::check('read_withdrawal') || Gate::check('create_withdrawal'))
                        <li
                            @if($first_url_part== 'withdrawal-request')
                            class="treeview active"
                            @else
                            class="treeview"
                            @endif>                        <a href="javascript:void(0)"><i class="ti-shopping-cart-full"></i> <span>فروش فرآورده</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_withdrawal',App\Models\WithdrawalRequest::class)
                                <li @if($first_url_part== 'withdrawal-request' && $second_url_part== 'index') class="active" @endif><a href="{{ route('withdrawal-request.index') }}">لیست درخواست ها</a></li>
                            @endcan
                            @can('create_withdrawal',App\Models\WithdrawalRequest::class)
                                <li @if($first_url_part== 'withdrawal-request' && $second_url_part== 'create') class="active" @endif><a href="{{ route('withdrawal-request.create') }}">ثبت درخواست</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @if(Gate::check('read_order') || Gate::check('create_order'))
                        <li
                            @if($first_url_part== 'order')
                            class="treeview active"
                            @else
                            class="treeview"
                            @endif>                        <a href="javascript:void(0)"><i class="ti-receipt"></i> <span>سفارشات</span> <i
                                class="fa fa-angle-left"></i></a>
                        <ul class="treeview-menu">
                            @can('read_order',App\Models\Order::class)
                                <li @if($first_url_part== 'order' && $second_url_part== 'index') class="active" @endif><a href="{{ route('order.index') }}">لیست سفارشات</a></li>
                            @endcan
                            @can('create_order',App\Models\Order::class)
                                <li @if($first_url_part== 'order' && $second_url_part== 'create') class="active" @endif><a href="{{ route('order.create') }}">ثبت سفارش جدید</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
