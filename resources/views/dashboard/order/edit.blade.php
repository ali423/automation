@extends('layouts.main')
@section('title','ویرایش سفارش')

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/imexport-print.css')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/daterange-picker.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش سفارش</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('order.update', $order) }}" class="needs-validation"
                            novalidate="" id="orderEditForm">
                            @method('PUT')
                            @csrf
                            <div class="form-row m-3">
                                <div class="form-group col">
                                    <label for="customer_id">{{ __('fields.customer')}}</label>
                                    <select id="customer_id" class="form-control" name="customer_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                            @if($order->customer_id == $customer->id )
                                                selected
                                                    @endif
                                                >{{$customer->name}}</option>
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
                                    <input type="text" name="deadline" value="{{ $order->deadline }}" id="deadline" class="form-control usage"
                                           autocomplete="off" required="">
                                    <div class="invalid-feedback">
                                        لطفاً {{  __('fields.deadline') }} را وارد کنید.
                                    </div>
                                </div>
                            </div>
                            <div id="order_formul" class="col-lg-12">
                                <p>اطلاعات سفارش</p>
                                @foreach($order->orderItems as $index => $item)
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    <div class="form-group col-md-3">
                                        <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
                                        <select id="commodity_id" class="form-control" name="commodity_id[{{ $index }}]" required>
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($commodities as $commodity)
                                                <option value="{{ $commodity->id }}"
                                                        @if($item->commodity_id == $commodity->id )
                                                        selected
                                                    @endif
                                                >{{$commodity->title}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ __('fields.commodity.name')}} را انتخاب کنید
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="unit_id"> {{ __('fields.unit') }}</label>
                                        <select id="unit_id" class="form-control" name="unit_id[{{ $index }}]" data-selected-unit="{{ $item->unit_id }}" required>
                                            <option value="">انتخاب کنید...</option>
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                                        <input type="number" id="amount" min="1" name="commodity_amount[{{ $index }}]" class="form-control"
                                               autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}"
                                               pattern="[0-9 .]" value="{{ $item->commodity_amount }}" required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="price"> {{  __('fields.sell-price_per_unit') }}</label>
                                        <input type="text" id="price" name="price[{{ $index }}]" value="{{ $item->price }}" class="form-control"
                                               autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="weight">وزن (کیلوگرم)</label>
                                        <input type="text" id="weight" class="form-control" readonly
                                               placeholder="وزن محاسبه می‌شود..." 
                                               value="{{ $item->weight_kg !== null ? number_format($item->weight_kg, 3) . ' کیلوگرم' : 'وزن تعریف نشده' }}">
                                    </div>
                                    @if($loop->count > 1)
                                    <div class="form-group col-md-1">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                            <i class="ti-close"></i>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                            </div>
                            
                            <!-- Total Weight Display -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <strong>مجموع وزن سفارش:</strong> 
                                        <span id="totalWeight">{{ $order->total_weight_kg !== null ? number_format($order->total_weight_kg, 3) . ' کیلوگرم' : 'نامشخص' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary mr-2">ویرایش</button>
                            <a href="{{ route('order.show', $order) }}" class="btn btn-danger">انصراف</a>
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
            // Add row
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
                renumberFormIndices(); // Renumber indices after adding
            });
            // Remove row
            $(document).on('click', '.remove-row', function () {
                $(this).closest('.form-row').remove();
                renumberFormIndices(); // Renumber indices after removal
                updateTotalWeight(); // Update total weight when row is removed
            });
            
            // Renumber form field indices sequentially
            function renumberFormIndices() {
                $('#order_formul .form-row').each(function(index) {
                    $(this).find('select[name^="commodity_id"]').attr('name', 'commodity_id[' + index + ']');
                    $(this).find('select[name^="unit_id"]').attr('name', 'unit_id[' + index + ']');
                    $(this).find('input[name^="commodity_amount"]').attr('name', 'commodity_amount[' + index + ']');
                    $(this).find('input[name^="price"]').attr('name', 'price[' + index + ']');
                });
            }
            
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
                    $('#order_formul .form-row').each(function() {
                        var weightValue = $(this).find('#weight').data('weight-value');
                        if (weightValue && !isNaN(weightValue)) {
                            totalWeight += parseFloat(weightValue);
                        }
                    });
                    $('#totalWeight').text(totalWeight.toFixed(3) + ' کیلوگرم');
                }, 100); // Small debounce for total weight updates
            }
            
            // Renumber indices before form submission to ensure all items are included
            $('#orderEditForm').on('submit', function(e) {
                renumberFormIndices();
            });
        });
    </script>
    <script>
        $(function() {
            // Renumber form field indices sequentially (define here for use in this script block)
            function renumberFormIndices() {
                $('#order_formul .form-row').each(function(index) {
                    $(this).find('select[name^="commodity_id"]').attr('name', 'commodity_id[' + index + ']');
                    $(this).find('select[name^="unit_id"]').attr('name', 'unit_id[' + index + ']');
                    $(this).find('input[name^="commodity_amount"]').attr('name', 'commodity_amount[' + index + ']');
                    $(this).find('input[name^="price"]').attr('name', 'price[' + index + ']');
                });
            }
            
            $('.usage').first().persianDatepicker();
            
            // Renumber form indices on page load to ensure sequential indices
            renumberFormIndices();
            
            // Populate unit dropdowns for existing order items when page loads
            $('#order_formul .form-row').each(function() {
                var $row = $(this);
                var commoditySelect = $row.find('select[name^="commodity_id"]');
                var unitSelect = $row.find('select[name^="unit_id"]');
                var commodityId = commoditySelect.val();
                var currentUnitId = unitSelect.attr('data-selected-unit');
                
                if (commodityId && commodityId !== '') {
                    // Get commodity units for existing items
                    $.ajax({
                        url: '/order/commodity-units/' + commodityId,
                        type: 'get',
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                // Clear and populate unit options
                                unitSelect.empty().append('<option value="">انتخاب کنید...</option>');
                                response.units.forEach(function(unit) {
                                    unitSelect.append('<option value="' + unit.id + '">' + unit.name + ' (' + unit.symbol + ')</option>');
                                });
                                
                                // Set the selected unit value for existing items
                                if (currentUnitId) {
                                    unitSelect.val(currentUnitId);
                                }
                                
                                // Calculate weight for existing items after units are loaded
                                calculateWeightForRow($row);
                            }
                        },
                        error: function(xhr, status, error) {
                            // Silent error handling - units will remain empty
                        }
                    });
                }
            });
        });
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/daterange-picker.js') }}"></script>
@endsection

