<!-- Side Nav -->
<div class="ecaps-sidenav" id="ecapsSideNav">
    <!-- Side Menu Area -->
    <div class="side-menu-area">
        <!-- Sidebar Menu -->
        <nav>
            <ul class="sidebar-menu" data-widget="tree">
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="fa fa-user"></i> <span>کاربران</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('user.index') }}">لیست کاربران</a></li>
                        <li><a href="{{ route('user.create') }}">افزودن کاربر جدید</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="fa fa-id-card-o"></i> <span>نقش ها</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('role.index') }}">لیست نقش ها</a></li>
                        <li><a href="{{ route('role.create') }}">افزودن نقش جدید</a></li>
                    </ul>
                </li>
                <li class="treeview active">
                    <a href="javascript:void(0)"><i class="icon_piechart"></i> <span>CRM</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li class="active"><a href="crm-dashboard.html">داشبورد</a></li>
                        <li><a href="crm-add-clint.html">ایجاد حساب</a></li>
                        <li><a href="crm-clint-list.html">لیست مشتریان</a></li>
                        <li><a href="crm-contact.html">مخاطبین</a></li>
                        <li><a href="crm-profile-customer.html">مشخصات</a></li>
                        <li><a href="crm-project.html">پروژه</a></li>
                        <li><a href="project-details.html">جزئیات پروژه</a></li>
                        <li><a href="crm-task.html">وظایف</a></li>
                        <li><a href="crm-leads.html"> جدول مدیریت  </a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_cart_alt"></i> <span> فروشگاه</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="eco-dashboard.html">داشبورد</a></li>
                        <li><a href="product.html"> محصولات </a></li>
                        <li><a href="e-add-product.html">اضافه کردن محصول</a></li>
                        <li><a href="product-details.html"> جزییات محصول</a></li>
                        <li><a href="order.html">سفارش</a></li>
                        <li><a href="cart.html">سبد خرید</a></li>
                        <li><a href="checkout.html">پرداخت</a></li>
                        <li><a href="invoice.html">صورتحساب</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_currency"></i> <span>مدریت صرافی</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="cry-dashboard.html">داشبورد</a></li>
                        <li><a href="cryp-exchange.html">تبادل</a></li>
                        <li><a href="crypto-wallet.html">کیف پول</a></li>
                        <li><a href="crypto-news.html">اخبار</a></li>
                        <li><a href="crypto-setting.html">تنظیمات</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_printer"></i> <span> مشاغل</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="job.html">اخبار استخدامی</a></li>
                        <li><a href="job-description.html">توضیحات</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_datareport"></i> <span>برنامه ها</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="calendar.html">تقویم</a></li>
                        <li><a href="widgets.html">ابزارک ها</a></li>
                        <li><a href="chat-box.html"> گپ</a></li>
                        <li><a href="timeline.html">جدول زمانی</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_document_alt"></i> <span>پست الکترونیک</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="mail-inbox.html">صندوق ورودی</a></li>
                        <li><a href="mail-view.html">مشاهده ایمیل</a></li>
                        <li><a href="compose-mail.html">ارسال  ایمیل</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_documents_alt"></i> <span>رابط کاربری </span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="alert.html">هشدار</a></li>
                        <li><a href="avatar.html">آواتار</a></li>
                        <li><a href="buttons.html">دکمه</a></li>
                        <li><a href="card.html">کارت</a></li>
                        <li><a href="notification.html">اطلاع رسانی</a></li>
                        <li><a href="general.html">عمومی</a></li>
                        <li><a href="progressbar.html">نوار پیشرفت</a></li>
                        <li><a href="preloader.html"> بارگذاری</a></li>
                        <li><a href="tab.html">برگه</a></li>
                        <li><a href="dropdown.html">رها کردن</a></li>
                        <li><a href="typography.html">تایپوگرافی</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_box-checked"></i><span>پیشرفته</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="blank.html">صفحه خالی</a></li>
                        <li><a href="gallery.html">گالری</a></li>
                        <li><a href="light-box-gallery.html">گالری 2 </a></li>
                        <li><a href="modals.html">مودال ها</a></li>
                        <li><a href="profile.html">مشخصات</a></li>
                        <li><a href="ribbons.html">روبان</a></li>
                        <li><a href="sweet-alert.html"> پنجره های زیبا</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_pencil-edit_alt"></i><span>صفحات عمومی</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="slider.html">اسلایدر</a></li>
                        <li><a href="range-slider.html"> تعیین قیمت</a></li>
                        <li><a href="contact.html">تماس با ما</a></li>
                        <li><a href="login.html">ورود به سیستم</a></li>
                        <li><a href="register.html">ثبت نام</a></li>
                        <li><a href="forget-password.html">فراموشی رمز عبور</a></li>
                        <li><a href="lock-screen.html">صفحه قفل</a></li>
                        <li><a href="404.html">404</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_datareport"></i><span>نمودار</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="chartist.html">  نمودار</a></li>
                        <li><a href="chart-js.html">نمودار Js</a></li>
                        <li><a href="morris-chart.html">نمودار موریس</a></li>
                        <li><a href="apex-chart.html">نمودار آپکس</a></li>

                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_menu-square_alt2"></i><span> فرم ها</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="basic-form.html">فرم اصلی</a></li>
                        <li><a href="advanced-elements.html">عناصر</a></li>
                        <li><a href="form-validation.html">اعتبار سنجی</a></li>
                        <li><a href="form-wizard.html"> فرم جادویی</a></li>
                        <li><a href="form-input-mask.html"> فرم قالب بندی</a></li>
                        <li><a href="file-upload.html">بارگذاری پرونده</a></li>
                        <li><a href="rating.html">رتبه بندی</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_table"></i><span>جداول</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="basic-table.html">جدول اساسی</a></li>
                        <li><a href="filter-table.html">جدول فیلتر</a></li>
                        <li><a href="data-table.html">جدول داده ها</a></li>
                        <li><a href="price-table.html">جدول قیمت</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_cogs"></i><span>آیکون ها</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="font-awesome.html">آیکون Awesome</a></li>
                        <li><a href="pe-7-stroke.html">نمونه ایکون</a></li>
                        <li><a href="matarial-icons.html">آیکون  متریال</a></li>
                        <li><a href="themify-icons.html">آیکون ti</a></li>
                        <li><a href="elegant-icons.html"> آیکون های زیبا</a></li>
                        <li><a href="et-line-icons.html">Et-line Icons</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_globe-2"></i><span>نقشه ها</span> <i class="fa fa-angle-left"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="vector-map.html">نمونه نقشه</a></li>
                        <li><a href="google-map.html">نقشه گوگل</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="icon_menu"></i> <span> آیتم</span> <i class="fa fa-angle-left"></i></a>

                    <ul class="treeview-menu">

                        <li class="treeview">
                            <a href="#">مرحله اول <i class="fa fa-angle-left"></i></a>
                            <ul class="treeview-menu">
                                <li><a href="#">سطح دو</a></li>
                                <li><a href="#">سطح دو</a></li>
                                <li><a href="#">سطح دو</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
