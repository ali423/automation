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
                            @include('dashboard.order.partials.order-form-fields', ['customers' => $customers])
                            <div id="order_formul" class="col-lg-12">
                                <p>اطلاعات سفارش</p>
                                @include('dashboard.order.partials.order-item-row', [
                                    'commodities' => $commodities,
                                    'index' => 0
                                ])
                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                            </div>
                            
                            <!-- Total Weight Display -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <strong>مجموع وزن سفارش:</strong> 
                                        <span id="totalWeight">0 کیلوگرم</span>
                                    </div>
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
                
                // Calculate weight when commodity changes
                calculateWeightForRow($(this).closest('.form-row'));
            });
            $(document).on('change', '#unit_id', function () {
                var new_price = $(this).closest('.form-row').find('#price').val();
                if (price == new_price && price != null ){
                    var unit = $(this).val();
                    var priceInput = $(this).closest('.form-row').find('#price');
                    // Price will be handled by the backend based on unit conversions
                    priceInput.val(price);
                }
                // Calculate weight when unit changes
                calculateWeightForRow($(this).closest('.form-row'));
            });
            
            // Calculate weight when amount changes - with debouncing
            var weightCalculationTimeout;
            $(document).on('input', '#amount', function () {
                var $row = $(this).closest('.form-row');
                clearTimeout(weightCalculationTimeout);
                weightCalculationTimeout = setTimeout(function() {
                    calculateWeightForRow($row);
                }, 300); // 300ms debounce
            });
            
            // Function to calculate weight for a specific row - optimized
            function calculateWeightForRow($row) {
                var commodityId = $row.find('#commodity_id').val();
                var unitId = $row.find('#unit_id').val();
                var amount = $row.find('#amount').val();
                var weightInput = $row.find('#weight');
                
                // Clear previous timeout for this row
                var rowId = $row.attr('data-row-id') || 'row_' + Date.now();
                $row.attr('data-row-id', rowId);
                
                if (commodityId && unitId && amount && amount > 0) {
                    // Show loading state
                    weightInput.val('در حال محاسبه...');
                    
                    $.ajax({
                        url: '/order/calculate-weight/' + commodityId + '/' + amount + '/' + unitId,
                        type: 'get',
                        dataType: 'json',
                        timeout: 5000, // 5 second timeout
                        success: function (response) {
                            // Only update if this is still the current row
                            if ($row.attr('data-row-id') === rowId) {
                                if (response.success) {
                                    weightInput.val(response.weight_formatted);
                                    weightInput.data('weight-value', response.weight);
                                } else {
                                    weightInput.val('خطا در محاسبه');
                                    weightInput.data('weight-value', 0);
                                }
                                updateTotalWeight();
                            }
                        },
                        error: function () {
                            // Only update if this is still the current row
                            if ($row.attr('data-row-id') === rowId) {
                                weightInput.val('خطا در محاسبه');
                                weightInput.data('weight-value', 0);
                                updateTotalWeight();
                            }
                        }
                    });
                } else {
                    weightInput.val('');
                    weightInput.data('weight-value', 0);
                    updateTotalWeight();
                }
            }
            
            // Function to update total weight display - optimized
            var totalWeightUpdateTimeout;
            function updateTotalWeight() {
                clearTimeout(totalWeightUpdateTimeout);
                totalWeightUpdateTimeout = setTimeout(function() {
                    var totalWeight = 0;
                    $('.form-row').each(function() {
                        var weightValue = $(this).find('#weight').data('weight-value');
                        if (weightValue && !isNaN(weightValue)) {
                            totalWeight += parseFloat(weightValue);
                        }
                    });
                    $('#totalWeight').text(totalWeight.toFixed(3) + ' کیلوگرم');
                }, 100); // Small debounce for total weight updates
            }
            // Add row - optimized version without AJAX
            $('#addRow').click(function () {
                var index = $('#order_formul .form-row').length;
                var html = `<div id="inputFormRow" class="form-row shadow p-4 mb-3">
                    <div class="form-group col-md-3">
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
                    <div class="form-group col-md-2">
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
                    <div class="form-group col-md-2">
                        <label for="weight">وزن (کیلوگرم)</label>
                        <input type="text" id="weight" class="form-control" readonly
                               placeholder="وزن محاسبه می‌شود...">
                    </div>
                    <div class="form-group col-md-1">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="ti-close"></i>
                        </button>
                    </div>
                </div>`;
                $('#newRow').append(html);
            });
            // Remove row
            $(document).on('click', '.remove-row', function () {
                $(this).closest('.form-row').remove();
                updateTotalWeight(); // Update total weight when row is removed
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
