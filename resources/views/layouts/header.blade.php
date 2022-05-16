<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>@yield('title', env('comp_name')) - سیستم اتوماسیون </title>
</head>

@include('layouts.styles')

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div id="ctn-preloader" class="ont-preloader">
            <div class="animation-preloader">
                <div class="spinner"></div>
                <div class="txt-loading">
                    <span data-text-preloader="{{ env('comp_name') }}" class="letters-loading">
                        {{ env('comp_name') }}
                    </span>
                </div>
                <p class="text-center">بارگذاری</p>
            </div>

            <div class="loader">
                <div class="row">
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader -->

    <!-- Setting Panel -->
    <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="ti-settings font-17"></i></div>
        <div id="theme-settings" class="settings-panel">
            <i class="settings-close zmdi zmdi-close font-22 font-weight-bold"></i>
            <p class="settings-heading mb-20 text-center">حالت منو :</p>

            <div class="sidebar-bg-options selected text-center px-5" id="sidebar-dark-theme">
                <div><span class="btn btn-outline-primary btn-sm btn-block">حالت تیره</span></div>
            </div>
            <div class="sidebar-bg-options text-center px-5" id="sidebar-color-theme">
                <div><span class="btn btn-outline-primary btn-sm btn-block">رنگارنگ</span></div>
            </div>
            <div class="sidebar-bg-options text-center px-5" id="sidebar-light-theme">
                <div><span class="btn btn-outline-primary btn-sm btn-block">حالت روشن</span></div>
            </div>

            <div class="quick-action p-3">
                <h4 class="card-title font-16 text-center">اقدامات سریع </h4>
                <hr>
                <div class="row">
                    <!-- Single Action -->
                    <div class="col-6">
                        <div class="action-area">
                            <div class="icon">
                                <i class="fa fa-archive"></i>
                            </div>
                            <div class="text">
                                <a href="#">
                                    <h5>سفارشات</h5>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Single Action -->
                    <div class="col-6">
                        <div class="action-area">
                            <div class="icon">
                                <i class="fa fa-wifi"></i>
                            </div>
                            <div class="text">
                                <a href="#">
                                    <h5>آپلودها</h5>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Single Action -->
                    <div class="col-6">
                        <div class="action-area">
                            <div class="icon">
                                <i class="fa fa-shopping-cart"></i>
                            </div>
                            <div class="text">
                                <a href="#">
                                    <h5> محصولات </h5>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Single Action -->
                    <div class="col-6">
                        <div class="action-area">
                            <div class="icon">
                                <i class="fa fa-cog"></i>
                            </div>
                            <div class="text">
                                <a href="#">
                                    <h5>تنظیمات</h5>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Single Action -->
                    <div class="col-6">
                        <div class="action-area">
                            <div class="icon">
                                <i class="fa fa-bar-chart-o"></i>
                            </div>
                            <div class="text">
                                <a href="#">
                                    <h5>سود</h5>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Single Action -->
                    <div class="col-6">
                        <div class="action-area">
                            <div class="icon">
                                <i class="fa fa-heart-o"></i>
                            </div>
                            <div class="text">
                                <a href="#">
                                    <h5> متن ساختگی </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================
******* Page Wrapper Area Start **********
======================================= -->
    <div class="ecaps-page-wrapper">
        <!-- Sidemenu Area -->
        <div class="ecaps-sidemenu-area">
            <!-- Desktop Logo -->
            <div class="ecaps-logo">
                <a href="index.html"><img class="desktop-logo" src="{{ asset('img/logo/darklogo.png') }}"
                        alt="لوگوی دسکتاپ"> <img class="small-logo" src="{{ asset('img/logo/icon.png') }}"
                        alt="آرم موبایل"></a>
            </div>
            @include('layouts.sidebar')
        </div>

        <!-- Page Content -->
        <div class="ecaps-page-content">
            <!-- Top Header Area -->
            <header class="top-header-area d-flex align-items-center justify-content-between">
                <div class="left-side-content-area d-flex align-items-center">
                    <!-- Mobile Logo -->
                    <div class="mobile-logo mr-3 mr-sm-4">
                        <a href="index.html"><img src="{{ asset('img/logo/icon.png') }}" alt="آرم موبایل"></a>
                    </div>

                    <!-- Triggers -->
                    <div class="ecaps-triggers mr-1 mr-sm-3">
                        <div class="menu-collasped rotate360" id="menuCollasped">
                            <i class="zmdi zmdi-sort-amount-desc"></i>
                        </div>
                        <div class="mobile-menu-open" id="mobileMenuOpen">
                            <i class="zmdi zmdi-sort-amount-desc"></i>
                        </div>
                    </div>
                </div>

                <div class="right-side-navbar d-flex align-items-center justify-content-end">
                    <!-- Mobile Trigger -->
                    <div class="right-side-trigger" id="rightSideTrigger">
                        <i class="fa fa-reorder"></i>
                    </div>

                    <!-- Top Bar Nav -->
                    <ul class="right-side-content d-flex align-items-center">
                        <!-- Left Side Nav -->
                        <li class="hide-phone app-search">
                            <form role="search" class=""><input type="text" placeholder="جستجو..."
                                    class="form-control"> <button type="submit" class="mr-0"><i
                                        class="fa fa-search"></i></button></form>
                        </li>

                        <li class="nav-item dropdown">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"><i class="fa fa-bell-o"
                                    aria-hidden="true"></i> <span class="active-status"></span></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <!-- Top Notifications Area -->
                                <div class="top-notifications-area">
                                    <!-- Heading -->
                                    <div class="notifications-heading">
                                        <div class="heading-title">
                                            <h6>اطلاعیه</h6>
                                        </div>
                                        <span>5 جدید</span>
                                    </div>

                                    <div class="notifications-box" id="notificationsBox">
                                        <a href="#" class="dropdown-item"><i
                                                class="ti-face-smile bg-success"></i><span>لورم ایپسوم متن ساختگی
                                                !</span></a>
                                        <a href="#" class="dropdown-item"><i
                                                class="zmdi zmdi-notifications-active bg-danger"></i><span>نام دامنه در
                                                روز سه شنبه منقضی می شود</span></a>
                                        <a href="#" class="dropdown-item"><i class="ti-check"></i><span>کمیسیون
                                                های شما ارسال شده است</span></a>
                                        <a href="#" class="dropdown-item"><i class="ti-heart bg-success"></i><span>شما
                                                یک مورد را فروختید!</span></a>
                                        <a href="#" class="dropdown-item"><i
                                                class="ti-bolt bg-warning"></i><span>لورم ایپسوم متن ساختگی با تولید
                                                سادگی</span></a>
                                        <a href="#" class="dropdown-item"><i
                                                class="ti-face-smile bg-success"></i><span>لورم ایپسوم متن ساختگی با
                                                تولید سادگی</span></a>
                                        <a href="#" class="dropdown-item"><i
                                                class="zmdi zmdi-notifications-active bg-danger"></i><span>نام دامنه در
                                                روز سه شنبه منقضی می شود</span></a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"><img
                                    src="{{ asset('img/member-img/4.png') }}" alt=""></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <!-- User Profile Area -->
                                <div class="user-profile-area">
                                    <div class="user-profile-heading">
                                        <!-- Thumb -->
                                        <div class="profile-img">
                                            <img class="chat-img mr-2" src="{{ asset('img/member-img/4.png') }}"
                                                alt="">
                                        </div>
                                        <!-- Profile Text -->
                                        <div class="profile-text">
                                            <h6>نام کاربر</h6>
                                            <span>توسعه دهنده</span>
                                        </div>
                                    </div>
                                    <a href="#" class="dropdown-item"><i
                                            class="zmdi zmdi-account profile-icon bg-primary" aria-hidden="true"></i>
                                        پروفایل من</a>
                                    <a href="#" class="dropdown-item"><i
                                            class="zmdi zmdi-email-open profile-icon bg-success"
                                            aria-hidden="true"></i> پیام ها</a>
                                    <a href="#" class="dropdown-item"><i
                                            class="zmdi zmdi-brightness-7 profile-icon bg-info" aria-hidden="true"></i>
                                        تنظیمات حساب</a>
                                    <a href="#" class="dropdown-item"><i
                                            class="zmdi zmdi-mouse profile-icon bg-danger" aria-hidden="true"></i>
                                        وظایف من</a>
                                    <a href="#" class="dropdown-item"><i
                                            class="zmdi zmdi-wifi-alt profile-icon bg-purple" aria-hidden="true"></i>
                                        پشتیبانی</a>
                                    <a href="{{ route('logout') }}" class="dropdown-item"><i
                                            class="ti-unlink profile-icon bg-warning" aria-hidden="true"></i> خروج از
                                        سیستم</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </header>
            <div class="main-content">
                <div class="container-fluid">
