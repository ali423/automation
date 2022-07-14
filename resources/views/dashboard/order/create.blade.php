@extends('layouts.main')
@section('title', 'ایجاد سفارش')

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/imexport-print.css')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/daterange-picker.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">سفارش جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('order.store') }}" class="needs-validation" novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="customer_id">{{ __('fields.customer')}}</label>
                                    <select id="customer_id" class="form-control" name="customer_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{$customer->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ __('fields.customer')}} را انتخاب کنید
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
                                    <select id="commodity_id" class="form-control" name="commodity_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($commodities as $commodity)
                                            <option value="{{ $commodity->id }}">{{$commodity->title}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ __('fields.commodity.name')}} را انتخاب کنید
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="amount"> {{  __('fields.sell-price_per_unit') }}</label>
                                    <input type="text" id="price" name="price" value="" class="form-control"
                                           autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('fields.deadline') }}</label>
                                    <input type="text" name="deadline" id="deadline" class="form-control usage"
                                          autocomplete="off" required="">
                                    <div class="invalid-feedback">
                                        لطفاً {{  __('fields.deadline') }} را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                                    <input type="number" id="amount" min="1" name="commodity_amount" class="form-control"
                                           autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}"
                                           pattern="[0-9 .]" required="">
                                    <div class="invalid-feedback">
                                        لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="unit"> {{ __('fields.unit') }}</label>
                                    <select id="unit" class="form-control" name="unit" required>
                                        <option value="">انتخاب کنید...</option>
                                        @foreach( __('fields.commodity.units') as $key=>$value)
                                            <option value="{{$key}}"
                                            >{{$value}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary mr-2">ثبت سفارش</button>
                            <a href="{{ route('order.index') }}" class="btn btn-danger">انصراف</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(function () {
            var price = null;
            $("#commodity_id").change(function () {
                var commodity_id = $("#commodity_id").val();
                $.ajax({
                    url: '/inventory-ajax/' + commodity_id,
                    type: 'get',
                    dataType: 'json',
                    success: function (response) {
                        price = response['price'];
                        $("#price").val(price);
                    }
                });
            });
            $("#unit").change(function () {
                var new_price = $("#price").val();

                if (price == new_price && price != null ){
                    var unit = $("#unit").val();
                    switch (unit) {
                        case "kg":
                            $("#price").val(price);
                            break;
                        case "keg":
                            $("#price").val(Math.round(price / 185));
                            break;
                        case "twenty_liters":
                            $("#price").val(Math.round(price / 17.8));
                            break;
                        default:
                            $("#price").val(price);
                            break;
                    }
                }
            });
        });
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/daterange-picker.js') }}"></script>
@endsection
