<!-- Side Nav -->
<div class="ecaps-sidenav" id="ecapsSideNav">
    <!-- Side Menu Area -->
    <div class="side-menu-area">
        <!-- Sidebar Menu -->
        <nav>
            <ul class="sidebar-menu" data-widget="tree">
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon-profile-male"></i> <span>کاربران</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('user.index') }}">لیست کاربران</a></li>
                        <li><a href="{{ route('user.create') }}">افزودن کاربر جدید</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_id"></i> <span>نقش ها</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('role.index') }}">لیست نقش ها</a></li>
                        <li><a href="{{ route('role.create') }}">افزودن نقش جدید</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon-search"></i> <span>فعالیت ها</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('activity.index') }}">لیست فعالیت ها</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon-layers"></i> <span>کالا های سیستم</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('commodity.index') }}">لیست کالا ها</a></li>
                        <li><a href="{{ route('commodity.create') }}">افزودن کالا جدید</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="ti-home"></i> <span>انبار ها</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('warehouse.index') }}">لیست انبار ها</a></li>
                        <li><a href="{{ route('warehouse.create') }}">افزودن انبار جدید</a></li>
                        <li><a href="{{ route('inventory.index') }}">وضعیت موجودی انبار</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="ti-truck"></i> <span>ورود کالا به انبار</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('importing-request.index') }}">لیست درخواست ها</a></li>
                        <li><a href="{{ route('importing-request.create') }}">ثبت درخواست</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="ti-shopping-cart"></i> <span>مشتری ها</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('customer.index') }}">لیست مشتریان</a></li>
                        <li><a href="{{ route('customer.create') }}">ثبت مشتری</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="ti-shopping-cart-full"></i> <span>فروش فرآورده</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('withdrawal-request.index') }}">لیست درخواست ها</a></li>
                        <li><a href="{{ route('withdrawal-request.create') }}">ثبت درخواست</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="ti-shopping-cart-full"></i> <span>سفارشات</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('order.index') }}">لیست سفارشات</a></li>
                        <li><a href="{{ route('order.create') }}">ثبت سفارش جدید</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
