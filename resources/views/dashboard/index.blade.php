@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')
    <!-- These plugins only need for the run this page -->
    <link rel="stylesheet" href="{{asset('js/default-assets/vector-map/jquery-jvectormap-2.0.2.css')}}">
@endsection

@section('content')

<!-- Main Content Area -->
        <div class="row">
            <!-- Single Widget -->
            <div class="col-sm-6 col-xl-3 box-margin height-card">
                <div class="card">
                    <div class="card-body d-flex justify-content-between">
                        <div class="crm-widget-content d-flex">
                            <div class="pity-chart mr-3">
                                <span class="pie">280/320</span>
                            </div>
                            <div class="content-text">
                                <p class="mb-1 font-15">پروژه</p>
                                <h5>84.8٪ <span class="font-13 text-muted">+ 34٪<i class="fa fa-level-up text-success" aria-hidden="true"></i></span></h5>
                            </div>
                        </div>
                        <div class="icon">
                            <a href="#"><i class="pe-7s-refresh font-16 text-muted font-weight-bold"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Single Widget -->
            <div class="col-sm-6 col-xl-3 box-margin height-card">
                <div class="card">
                    <div class="card-body d-flex justify-content-between">
                        <div class="crm-widget-content d-flex">
                            <div class="pity-chart mr-3">
                                <span class="pie2">200/320</span>
                            </div>
                            <div class="content-text">
                                <p class="mb-1 font-15">وظایف</p>
                                <h5>170 <span class="font-13 text-muted">+ 45٪<i class="fa fa-level-up text-success" aria-hidden="true"></i></span></h5>
                            </div>
                        </div>
                        <div class="icon">
                            <a href="#"><i class="pe-7s-refresh font-16 text-muted font-weight-bold"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Single Widget -->
            <div class="col-sm-6 col-xl-3 box-margin height-card">
                <div class="card">
                    <div class="card-body d-flex justify-content-between">
                        <div class="crm-widget-content d-flex">
                            <div class="pity-chart mr-3">
                                <span class="pie3">240/320</span>
                            </div>
                            <div class="content-text">
                                <p class="mb-1 font-15">هفتگی</p>
                                <h5>2256 تومان <span class="font-13 text-muted">+ 55٪<i class="fa fa-level-up text-success" aria-hidden="true"></i></span></h5>
                            </div>
                        </div>
                        <div class="icon">
                            <a href="#"><i class="pe-7s-refresh font-16 text-muted font-weight-bold"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Single Widget -->
            <div class="col-sm-6 col-xl-3 box-margin height-card">
                <div class="card">
                    <div class="card-body d-flex justify-content-between">
                        <div class="crm-widget-content d-flex">
                            <div class="pity-chart mr-3">
                                <span class="pie4">220/320</span>
                            </div>
                            <div class="content-text">
                                <p class="mb-1 font-15">مشتری</p>
                                <h5>45 <span class="font-13 text-muted">+ 30٪<i class="fa fa-level-up text-success" aria-hidden="true"></i></span></h5>
                            </div>
                        </div>
                        <div class="icon">
                            <a href="#"><i class="pe-7s-refresh font-186 text-muted font-weight-bold"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 box-margin height-card">
                <div class="card">
                    <div class="card-body">
                        <div class="crm-chart">
                            <div id="apex7"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 box-margin height-card">
                <div class="card bg-gradient-dark">
                    <div class="card-body px-4">
                        <h6 class="text-white card-title mb-0"><span class="text-danger mr-2">●</span>  متن ساختگی </h6>
                        <div class="card-body text-center">
                            <!-- Avatar -->
                            <a href="#" class="avatar avatar-lg rounded-circle">
                                <img class="user-thumb" alt="تصویر" src="img/shop-img/21.png">
                            </a>
                            <!-- Title -->
                            <h5 class="font-20 mt-4 mb-1 text-white">توسعه وب سایت</h5>
                            <p class="text-white font-13">لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.</p>

                            <!-- Actions -->
                            <div class="actions actions-dark d-flex justify-content-between px-4 mt-4">
                                <a href="#" class="action-item">
                                    <i class="fa fa-pie-chart font-20" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="action-item">
                                    <i class="fa fa-user-circle-o font-20" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="action-item">
                                    <i class="fa fa-info-circle font-20" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>

                        <div class="row justify-content-between align-items-center">
                            <div class="col-6">
                                <div class="icon-thumb">
                                    <img src="img/bg-img/icon-4.png" alt="">
                                </div>
                            </div>
                            <div class="col-auto text-center">
                                <span class="d-block font-22 mb-0 text-white">07 </span>
                                <span class="d-block text-white">روز تاخیر</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 box-margin">
                <div class="card mb-30">
                    <div class="card-body">
                        <h6 class="card-title"> فروش</h6>
                        <div class="crm-statement border p-4">
                            <h6 class="card-title text-center">میانگین</h6>
                            <div class="row">
                                <div class="col text-center mb-15">
                                    <h6 class="font-14 mb-1">روزانه</h6>
                                    <p class="font-16 mb-0">300 تومان</p>
                                </div>
                                <div class="col text-center mb-15">
                                    <h6 class="font-14 mb-1">هفتگی</h6>
                                    <p class="font-16 mb-0">1400 تومان</p>
                                </div>
                                <div class="col text-center mb-15">
                                    <h6 class="font-14 mb-1">ماهانه</h6>
                                    <p class="font-16 mb-0">5000 تومان</p>
                                </div>
                            </div>
                            <div class="progress h-6 mt-15 mb-10">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="mb-0">پیشرفت خوبی داشتیم!</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="text-danger card-title">درآمد امروز</h5>
                        <h4 class="text-center font-36 text-info mb-0"><i class="fa fa-money text-success mr-2 font-32"></i>3،258 تومان</h4>
                        <div class="d-flex justify-content-between mt-30">
                            <p class="font-16 mb-0">درآمد خالص</p>
                            <p class="font-16 mb-0">3050 تومان</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 box-margin">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">سفارشات جدید</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">شرکت</th>
                                    <th scope="col">تاریخ</th>
                                    <th scope="col">وضعیت</th>
                                    <th scope="col">مدیریت  </th>
                                    <th scope="col">عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="media">
                                            <img src="img/shop-img/20.jpg" class="chat-img mr-3" alt="...">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1"> متن ساختگی </h6>
                                                <span class="font-13">نام کاربری</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="txt-14">
                                        12.011.19
                                    </td>
                                    <td>
                                                        <span class="badge badge-soft-success">
                                                            در حال پردازش
                                                        </span>
                                    </td>
                                    <td>
                                        <div class="media">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1">جان دو</h6>
                                                <span class="font-13">عکاس</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="ویرایش">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="آرشیو">
                                            <i class="fa fa-archive"></i>
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="media">
                                            <img src="img/shop-img/21.png" class="chat-img mr-3" alt="...">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1"> متن ساختگی </h6>
                                                <span class="font-13">نام کاربری</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="txt-14">
                                        14.011.19
                                    </td>
                                    <td>
                                                        <span class="badge badge-soft-warning">
                                                            در حال پردازش
                                                        </span>
                                    </td>
                                    <td>
                                        <div class="media">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1">جان دو</h6>
                                                <span class="font-13">دلوپر</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="ویرایش">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="آرشیو">
                                            <i class="fa fa-archive"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="media">
                                            <img src="img/shop-img/22.png" class="chat-img mr-3" alt="...">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1"> متن ساختگی </h6>
                                                <span class="font-13">نام کاربری</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="txt-14">
                                        18.011.19
                                    </td>
                                    <td>
                                                        <span class="badge badge-soft-info">
                                                            در حال پردازش
                                                        </span>
                                    </td>
                                    <td>
                                        <div class="media">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1">جان دو</h6>
                                                <span class="font-13">دلوپر</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="ویرایش">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="آرشیو">
                                            <i class="fa fa-archive"></i>
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="media">
                                            <img src="img/shop-img/23.png" class="chat-img mr-3" alt="...">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1"> متن ساختگی </h6>
                                                <span class="font-13">نام کاربری</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="txt-14">
                                        22.011.19
                                    </td>
                                    <td>
                                                        <span class="badge badge-soft-primary">
                                                            در حال پردازش
                                                        </span>
                                    </td>
                                    <td>
                                        <div class="media">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1">جان دو</h6>
                                                <span class="font-13">عکاس</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="ویرایش">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="آرشیو">
                                            <i class="fa fa-archive"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="media">
                                            <img src="img/shop-img/20.jpg" class="chat-img mr-3" alt="...">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1"> متن ساختگی </h6>
                                                <span class="font-13">نام کاربری</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="txt-14">
                                        12.011.19
                                    </td>
                                    <td>
                                                        <span class="badge badge-soft-warning">
                                                            در حال پردازش
                                                        </span>
                                    </td>
                                    <td>
                                        <div class="media">
                                            <div class="media-body company-details">
                                                <h6 class="font-15 mb-1">جان دو</h6>
                                                <span class="font-13">عکاس</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="ویرایش">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="" data-original-title="آرشیو">
                                            <i class="fa fa-archive"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7 box-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">  نقشه</h4>
                        <div class="map--body mb-30">
                            <div id="world-map-markers" class="height-300"></div>
                        </div>
                        <div class="map-contnet">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>کشور</th>
                                        <th>محل</th>
                                        <th>بازدید کنندگان</th>
                                        <th>رشد</th>
                                        <th>وضعیت</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <img class="chat-img" src="img/shop-img/l1.jpg" alt="">
                                        </td>
                                        <td>هند</td>
                                        <td>12،117</td>
                                        <td>8.15٪</td>
                                        <td>
                                            <i class="fa fa-line-chart font-18 text-primary"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img class="chat-img" src="img/shop-img/l2.jpg" alt="">
                                        </td>
                                        <td>بلژیک</td>
                                        <td>64،637</td>
                                        <td>3.06٪</td>
                                        <td>
                                            <i class="fa fa-line-chart font-18 text-primary"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img class="chat-img" src="img/shop-img/l3.jpg" alt="">
                                        </td>
                                        <td>فرانسه</td>
                                        <td>81،848</td>
                                        <td>6.83٪</td>
                                        <td>
                                            <i class="fa fa-line-chart font-18 text-primary"></i>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-30">
                            <div class="card-header bg-white border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">17 آبان</h6>
                                    </div>
                                    <div class="text-right">
                                        <div class="dashboard-dropdown">
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" type="button" id="dashboardDropdown56" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ti-more"></i></button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dashboardDropdown56">
                                                    <a class="dropdown-item" href="#"><i class="ti-pencil-alt"></i> ویرایش کنید</a>
                                                    <a class="dropdown-item" href="#"><i class="ti-settings"></i> تنظیمات</a>
                                                    <a class="dropdown-item" href="#"><i class="ti-eraser"></i> برداشتن</a>
                                                    <a class="dropdown-item" href="#"><i class="ti-trash"></i> حذف</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body text-center"><a href="#">
                                    <img class=" border-radius-50 user-thumb " src="img/shop-img/17.jpg" alt="">
                                </a>

                                <h5 class="card-title mt-20 font-17"><a class="text-dark" href="project-details.html">وب سایت </a></h5>
                                <div class="avatar-area mb-15">
                                    <div class="img-group">
                                        <a class="user-avatar user-avatar-group" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="نام کاربر"><img src="img/member-img/2.png" alt="کاربر" class="rounded-circle"> </a>
                                        <a class="user-avatar user-avatar-group" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="نام کاربر"><img src="img/member-img/3.png" alt="کاربر" class="rounded-circle"> </a>
                                        <a class="user-avatar user-avatar-group" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="نام کاربر"><img src="img/member-img/4.png" alt="کاربر" class="rounded-circle"> </a>
                                        <a class="user-avatar user-avatar-group" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="نام کاربر"><img src="img/member-img/5.png" alt="کاربر" class="rounded-circle"></a>
                                    </div>
                                </div>
                                <span class="clearfix"></span>
                                <span class="badge badge-pill badge-info font-14">در انتظار</span>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="actions d-flex justify-content-between p-3">
                                    <a href="#" class="action-item text-primary">
                                        <i class="ti-plus font-weight-bold"></i>
                                    </a>
                                    <a href="#" class="action-item text-success">
                                        <i class="ti-email font-weight-bold"></i>
                                    </a>
                                    <a href="#" class="action-item text-danger">
                                        <i class="ti-close font-weight-bold"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-30">
                            <div class="card-header bg-white border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">19 آبان</h6>
                                    </div>
                                    <div class="text-right">
                                        <div class="dashboard-dropdown">
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" type="button" id="dashboardDropdown58" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ti-more"></i></button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dashboardDropdown58">
                                                    <a class="dropdown-item" href="#"><i class="ti-pencil-alt"></i> ویرایش کنید</a>
                                                    <a class="dropdown-item" href="#"><i class="ti-settings"></i> تنظیمات</a>
                                                    <a class="dropdown-item" href="#"><i class="ti-eraser"></i> برداشتن</a>
                                                    <a class="dropdown-item" href="#"><i class="ti-trash"></i> حذف</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body text-center"><a href="#">
                                    <img class=" border-radius-50 user-thumb " src="img/shop-img/18.jpg" alt="">
                                </a>

                                <h5 class="card-title mt-20 font-17"><a class="text-dark" href="project-details.html">رابط کاربر وب سایت</a></h5>
                                <div class="avatar-area mb-15">
                                    <div class="img-group">
                                        <a class="user-avatar user-avatar-group" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="نام کاربر"><img src="img/member-img/2.png" alt="کاربر" class="rounded-circle"> </a>
                                        <a class="user-avatar user-avatar-group" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="نام کاربر"><img src="img/member-img/3.png" alt="کاربر" class="rounded-circle"> </a>
                                        <a class="user-avatar user-avatar-group" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="نام کاربر"><img src="img/member-img/4.png" alt="کاربر" class="rounded-circle"> </a>
                                        <a class="user-avatar user-avatar-group" href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="نام کاربر"><img src="img/member-img/5.png" alt="کاربر" class="rounded-circle"></a>
                                    </div>
                                </div>
                                <span class="clearfix"></span>
                                <span class="badge badge-pill badge-info font-14">در انتظار</span>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="actions d-flex justify-content-between p-3">
                                    <a href="#" class="action-item text-primary">
                                        <i class="ti-plus font-weight-bold"></i>
                                    </a>
                                    <a href="#" class="action-item text-success">
                                        <i class="ti-email font-weight-bold"></i>
                                    </a>
                                    <a href="#" class="action-item text-danger">
                                        <i class="ti-close font-weight-bold"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-30">
                            <div class="card-body pb-0">
                                <div class="single-conatct-area">
                                    <div class="conatct-thumb">
                                        <img src="img/member-img/3.png" alt="">
                                    </div>
                                    <!-- Member Info -->
                                    <div class="member-info text-center">
                                        <h6>نام کاربری</h6>
                                        <p class="mb-1">lim.58@gmail.com</p>
                                        <p class="text-dark font-14">09121234567</p>
                                    </div>
                                    <!-- Icon -->
                                    <div class="contact-social-icon d-flex justify-content-between px-4">
                                        <a href="#" data-toggle="tooltip" title="" data-original-title="Facebook">
                                            <i class="fa fa-facebook" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" data-toggle="tooltip" title="" data-original-title="Dribbble">
                                            <i class="fa fa-dribbble" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" data-toggle="tooltip" title="" data-original-title="Twitter">
                                            <i class="fa fa-twitter" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" data-toggle="tooltip" title="" data-original-title="Instagram">
                                            <i class="fa fa-instagram" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <!-- Footer -->
                                    <div class="contact-footer d-flex justify-content-between">
                                        <a class="font-13" href="#">پروژه </a>
                                        <a class="font-13" href="#">مشخصات را ببینید</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-30">
                            <div class="card-body pb-0">
                                <div class="single-conatct-area">
                                    <div class="conatct-thumb">
                                        <img src="img/member-img/5.png" alt="">
                                    </div>
                                    <!-- Member Info -->
                                    <div class="member-info text-center">
                                        <h6>نام کاربری</h6>
                                        <p class="mb-1">jhon.58@gmail.com</p>
                                        <p class="text-dark font-14">09121234567</p>
                                    </div>
                                    <!-- Icon -->
                                    <div class="contact-social-icon d-flex justify-content-between px-4">
                                        <a href="#" data-toggle="tooltip" title="" data-original-title="Facebook">
                                            <i class="fa fa-facebook" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" data-toggle="tooltip" title="" data-original-title="Dribbble">
                                            <i class="fa fa-dribbble" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" data-toggle="tooltip" title="" data-original-title="Twitter">
                                            <i class="fa fa-twitter" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" data-toggle="tooltip" title="" data-original-title="Instagram">
                                            <i class="fa fa-instagram" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <!-- Footer -->
                                    <div class="contact-footer d-flex justify-content-between">
                                        <a class="font-13" href="#">پروژه </a>
                                        <a class="font-13" href="#">مشخصات را ببینید</a>
                                    </div>
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
@endsection
