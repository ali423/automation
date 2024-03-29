@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')
<link rel="stylesheet" href="{{ asset('css/imexport-print.css')}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">انبار جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('warehouse.store') }}" class="needs-validation" novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="title"> {{ __('fields.title') }}</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                        id="title" placeholder="عنوان انبار" required="">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان انبار را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="capacity"> {{ __('fields.capacity') }}</label>
                                    <input type="number" min="1" name="capacity" value="{{ old('capacity') }}"
                                        class="form-control" id="capacity" autocomplete="off"
                                        placeholder="{{ __('fields.capacity') }}" pattern="[0-9 .]" required="">
                                    <div class="invalid-feedback">
                                        لطفاً ظرفیت انبار را وارد کنید.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="type"> {{ __('fields.type') }}</label>
                                    <select id="type" class="form-control" name="type" required>
                                        <option value="">انتخاب کنید...</option>
                                        @foreach (__('fields.warehouse.types') as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">نوع انبار را انتخاب کنید</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status"> {{ __('fields.status') }}</label>
                                    <select id="status" class="form-control" name="status" required>
                                        @foreach (__('fields.warehouse.status') as $key => $value)
                                            <option value="{{ $key }}"
                                                @if ($key == 'active') selected @endif>{{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">وضعیت انبار را انتخاب کنید</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ثبت انبار</button>
                            <a href="{{ route('warehouse.index') }}" class="btn btn-danger">انصراف</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 height-card box-margin">
            <div class="card">
                <div class="card-body">
                    <!-- Stacked Bar -->
                    <div id="panel-15" class="panel">
                        <h4 class="card-title">نمونه چارت برای انبار</h4>
                        <div id="mychart" class="row">
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    200 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-primary" data-current="20"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    100 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-success" data-current="90"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    50 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-danger" data-current="50"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    20 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-secondary" data-current="20"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    10 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-warning" data-current="90"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    70 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-danger" data-current="50"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    40 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-primary" data-current="20"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    60 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-success" data-current="90"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    45 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-danger" data-current="50"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    33 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-primary" data-current="20"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    88 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-success" data-current="90"></span>
                                </div>
                            </div>
                            <div class="col mr-1 mt-5 mt-3">
                                <span class="size">
                                    68 لیتر
                                </span>
                                <div class="full-size">
                                    <span class="current-size bg-danger" data-current="50"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="invoice" class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                {{-- <h4 class="card-title"></h4> --}}
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <img src="{{ asset('img/logo/darklogo.png') }}" class="logo" />
                            <h4>ورود و خروج کالا</h4>
                            <div>تاریخ: <span>1400/01/02</span></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>خریدار/ نماینده خریدار: <span>علیرضا مشایخی</span></div>
                            <div>شماره سفارش: <span>14000511</span></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <table class="table-borderless">
                                <colgroup>
                                    <col span="1" style="width: 5%;">
                                    <col span="1" style="width: 55%;">
                                    <col span="1" style="width: 15%;">
                                    <col span="1" style="width: 25%;">
                                 </colgroup>
                                <tr class="table-header">
                                    <th scope="col">ردیف</th>
                                    <th scope="col">کالای ورودی</th>
                                    <th scope="col">تعداد / مقدار</th>
                                    <th scope="col">توضیحات</th>
                                </tr>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>کالا 1</td>
                                    <td>20</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>کالا 2</td>
                                    <td>30</td>
                                    <td>رنگ زرد جایگزین شد</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>کالا 3</td>
                                    <td>25</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                        <div class="mb-5">
                            اینجانب <span>علیرضا مشایخی</span>
                            راننده خودرو به شماره پلاک <div class="pelak">77 الف 777</div><div class="pelak">77</div>
                            شماره تماس <span>091212121212</span>
                            محموله فوق را تحویل گرفتم.
                        </div>
                        <div class="d-flex justify-content-around align-items-center mb-3">
                            <h6>امضاء تحویل گیرنده کالا</h6>
                            <h6>امضاء متصدی شرکت</h6>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>

    {{-- chart --}}
    <script src="{{ asset('js/store-chart/store-chart.js') }}"></script>
@endsection
