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
                            <div class="form-row m-3">
                                <div class="form-group col">
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
                            </div>
                            <div class="form-row m-3">
                                <div class="form-group col">
                                    <label for="deadline">{{ __('fields.deadline') }}</label>
                                    <input type="text" name="deadline" id="deadline" class="form-control usage" autocomplete="off" required="">
                                    <div class="invalid-feedback">
                                        لطفاً {{  __('fields.deadline') }} را وارد کنید.
                                    </div>
                                </div>
                            </div>
                            <div id="order_formul" class="col-lg-12">
                                <p>اطلاعات سفارش</p>
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    <div class="form-group col-md-4">
                                        <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
                                        <select id="commodity_id" class="form-control" name="commodity_id[0]" required>
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($commodities as $commodity)
                                                <option value="{{ $commodity->id }}">{{$commodity->title}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ __('fields.commodity.name')}} را انتخاب کنید
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="unit_id"> {{ __('fields.unit') }}</label>
                                        <select id="unit_id" class="form-control" name="unit_id[0]" required>
                                            <option value="">انتخاب کنید...</option>
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                                        <input type="number" id="amount" min="1" name="commodity_amount[0]" class="form-control"
                                               autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}"
                                               pattern="[0-9 .]" required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="price"> {{  __('fields.sell-price_per_unit') }}</label>
                                        <input type="text" id="price" name="price[0]" value="" class="form-control"
                                               autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}">
                                    </div>
                                </div>
                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
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
            $(document).on('change', '#commodity_id', function () {
                var commodity_id = $(this).val();
                var priceInput = $(this).closest('.form-row').find('#price');
                var unitSelect = $(this).closest('.form-row').find('#unit_id');
                
                // Get commodity units
                $.ajax({
                    url: '/order/commodity-units/' + commodity_id,
                    type: 'get',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Clear and populate unit options
                            unitSelect.empty().append('<option value="">انتخاب کنید...</option>');
                            response.units.forEach(function(unit) {
                                unitSelect.append('<option value="' + unit.id + '">' + unit.name + ' (' + unit.symbol + ')</option>');
                            });
                        }
                    }
                });
                
                // Get commodity price
                $.ajax({
                    url: '/inventory-ajax/' + commodity_id,
                    type: 'get',
                    dataType: 'json',
                    success: function (response) {
                        price = response['price'];
                        priceInput.val(price);
                    }
                });
            });
            $(document).on('change', '#unit_id', function () {
                var new_price = $(this).closest('.form-row').find('#price').val();
                if (price == new_price && price != null ){
                    var unit = $(this).val();
                    var priceInput = $(this).closest('.form-row').find('#price');
                    // Price will be handled by the backend based on unit conversions
                    priceInput.val(price);
                }
            });
            // Add row
            $('#addRow').click(function () {
                var index = $('#order_formul .form-row').length;
                var html = `<div id="inputFormRow" class="form-row shadow p-4 mb-3">
                    <div class="form-group col-md-4">
                        <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
                        <select id="commodity_id" class="form-control" name="commodity_id[${index}]" required>
                            <option value="">انتخاب کنید</option>
                            @foreach ($commodities as $commodity)
                                <option value="{{ $commodity->id }}">{{$commodity->title}}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            {{ __('fields.commodity.name')}} را انتخاب کنید
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="unit_id"> {{ __('fields.unit') }}</label>
                        <select id="unit_id" class="form-control" name="unit_id[${index}]" required>
                            <option value="">انتخاب کنید...</option>
                        </select>
                        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                        <input type="number" id="amount" min="1" name="commodity_amount[${index}]" class="form-control"
                               autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}"
                               pattern="[0-9 .]" required="">
                        <div class="invalid-feedback">
                            لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="price"> {{  __('fields.sell-price_per_unit') }}</label>
                        <input type="text" id="price" name="price[${index}]" value="" class="form-control"
                               autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}">
                    </div>
                    <i id="removeRow" type="button" class="ti-close" style="cursor:pointer; font-size:1.5rem; color:#dc3545; margin-top:2rem;"></i>
                </div>`;
                $('#newRow').append(html);
                // Initialize datepicker only on the newly added deadline field
                var $lastDeadline = $('#newRow .usage').last();
                if ($lastDeadline.data('persianDatepicker')) {
                    $lastDeadline.data('persianDatepicker').remove();
                    $lastDeadline.removeData('persianDatepicker');
                }
                $lastDeadline.persianDatepicker();
            });
            // Remove row
            $(document).on('click', '#removeRow', function () {
                $(this).closest('.form-row').remove();
            });
        });
    </script>
    <script>
        $(function() {
            $('.usage').first().persianDatepicker();
        });
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/daterange-picker.js') }}"></script>
@endsection
