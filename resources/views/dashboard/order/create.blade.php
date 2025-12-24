@extends('layouts.main')
@section('title', 'ایجاد سفارش')

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/imexport-print.css')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/daterange-picker.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
        .weight-calculating {
            position: relative;
            padding-right: 30px;
        }
        .weight-calculating::after {
            content: '';
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }
    </style>
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
            
            // Initialize commodity data cache from existing rows (if any)
            $('.form-row').each(function() {
                var $row = $(this);
                var rowId = $row.attr('data-row-id') || 'row_' + Date.now();
                $row.attr('data-row-id', rowId);
                
                // Check if row has initial commodity data
                var commodityUnitId = $row.attr('data-commodity-unit-id');
                var commodityWeightPerUnit = $row.attr('data-commodity-weight-per-unit');
                if (commodityUnitId && commodityWeightPerUnit) {
                    commodityDataCache[rowId] = {
                        unit_id: parseInt(commodityUnitId),
                        weight_per_unit: parseFloat(commodityWeightPerUnit)
                    };
                }
            });

            // Commodity search triggered only on button click
            $(document).on('click', '.btn-commodity-search', function () {
                var $row = $(this).closest('.form-row');
                var term = $row.find('.commodity-search-input').val().trim();
                var $select = $row.find('#commodity_id');
                var $help = $row.find('.commodity-help-text');

                // If search term is empty, restore original full list (if stored) and return
                if (!term) {
                    var original = $select.data('original-options');
                    if (original) {
                        $select.html(original);
                        $select.prop('disabled', false);
                    }
                    if ($help.length) {
                        $help.removeClass('text-danger')
                            .text('ابتدا نام کالا را جستجو کرده و سپس از لیست بالا انتخاب کنید.');
                    }
                    return;
                }

                if ($help.length) {
                    $help.removeClass('text-danger')
                        .text('در حال جستجوی کالا...');
                }

                // Store original options once so we can restore later
                if (!$select.data('original-options')) {
                    $select.data('original-options', $select.html());
                }

                $select.prop('disabled', true)
                    .empty()
                    .append('<option value="">در حال جستجو...</option>');

                $.ajax({
                    url: '{{ route('commodity.search') }}',
                    type: 'get',
                    dataType: 'json',
                    data: { search: term },
                    success: function (data) {
                        $select.empty();
                        if (!data.length) {
                            $select.append('<option value="">موردی یافت نشد</option>');
                            if ($help.length) {
                                $help.text('کالایی با این نام یافت نشد.');
                            }
                        } else {
                            $select.append('<option value="">انتخاب کنید...</option>');
                            data.forEach(function (item) {
                                var option = $('<option></option>')
                                    .attr('value', item.id)
                                    .text(item.text);
                                if (item.discount_percentage !== null && item.discount_percentage !== undefined) {
                                    option.attr('data-discount', item.discount_percentage);
                                }
                                $select.append(option);
                            });
                            if ($help.length) {
                                $help.text('لطفاً از لیست بالا یک کالا را انتخاب کنید.');
                            }
                        }
                        $select.prop('disabled', false);
                    },
                    error: function () {
                        $select.empty()
                            .append('<option value="">خطا در جستجو</option>')
                            .prop('disabled', false);
                        if ($help.length) {
                            $help.addClass('text-danger')
                                .text('خطا در جستجو. دوباره تلاش کنید.');
                        }
                    }
                });
            });

            // When user clears the search box, restore original full list (if any)
            $(document).on('input', '.commodity-search-input', function () {
                var $row = $(this).closest('.form-row');
                var term = $(this).val().trim();
                var $select = $row.find('#commodity_id');
                var $help = $row.find('.commodity-help-text');

                if (!term) {
                    var original = $select.data('original-options');
                    if (original) {
                        $select.html(original);
                        $select.prop('disabled', false);
                    }
                    if ($help.length) {
                        $help.removeClass('text-danger')
                            .text('ابتدا نام کالا را جستجو کرده و سپس از لیست بالا انتخاب کنید.');
                    }
                }
            });

            // Store commodity data per row for caching
            var commodityDataCache = {};
            
            $(document).on('change', '#commodity_id', function () {
                var $row = $(this).closest('.form-row');
                var commodity_id = $(this).val();
                var priceInput = $row.find('#price');
                var unitSelect = $row.find('#unit_id');
                var helpText = $(this).closest('.form-group').find('.commodity-help-text');
                var weightInput = $row.find('#weight');

                if (helpText.length) {
                    if (commodity_id) {
                        var selectedText = $(this).find('option:selected').text();
                        helpText.removeClass('text-danger')
                            .text('کالای انتخاب شده: ' + selectedText);
                    } else {
                        helpText.removeClass('text-danger')
                            .text('ابتدا نام کالا را جستجو کرده و سپس از لیست بالا انتخاب کنید.');
                    }
                }
                
                // Clear weight and commodity cache when commodity changes
                weightInput.val('').removeClass('weight-calculating').data('weight-value', 0);
                delete commodityDataCache[$row.attr('data-row-id')];
                
                // Set loading states
                unitSelect.prop('disabled', true).empty().append('<option value="">در حال بارگذاری...</option>');
                priceInput.prop('disabled', true).attr('placeholder','در حال بارگذاری...');

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
                            unitSelect.prop('disabled', false);
                            
                            // Cache commodity data for weight calculation
                            var rowId = $row.attr('data-row-id') || 'row_' + Date.now();
                            $row.attr('data-row-id', rowId);
                            commodityDataCache[rowId] = {
                                unit_id: response.commodity.unit_id,
                                weight_per_unit: response.commodity.weight_per_unit
                            };
                        } else {
                            unitSelect.empty().append('<option value="">خطا در بارگذاری</option>').prop('disabled', false);
                        }
                    },
                    error: function () {
                        unitSelect.empty().append('<option value="">خطا در بارگذاری</option>').prop('disabled', false);
                    }
                });
                
                // Get commodity price
                $.ajax({
                    url: '/inventory-ajax/' + commodity_id,
                    type: 'get',
                    dataType: 'json',
                    success: function (response) {
                        price = (response && response.data) ? response.data.price : null;
                        var formattedPrice = price ? Math.round(parseFloat(price)).toString() : '';
                        priceInput.val(formattedPrice).attr('placeholder','').prop('disabled', false);
                    },
                    error: function () {
                        priceInput.val('').attr('placeholder','نامشخص').prop('disabled', false);
                    }
                });
                
                // Pre-fill discount from commodity
                var $discountInput = $row.find('#discount_percentage');
                var commodityDiscount = $(this).find('option:selected').data('discount');
                if (!$discountInput.val() && commodityDiscount !== null && commodityDiscount !== undefined) {
                    $discountInput.val(commodityDiscount);
                }
                
                // Calculate weight when commodity changes - with debouncing
                var rowId = $row.attr('data-row-id') || 'row_' + Date.now();
                $row.attr('data-row-id', rowId);
                clearTimeout($row.data('weight-debounce-timeout'));
                var timeout = setTimeout(function() {
                    calculateWeightForRow($row);
                }, 300);
                $row.data('weight-debounce-timeout', timeout);
            });
            $(document).on('change', '#unit_id', function () {
                var $row = $(this).closest('.form-row');
                var new_price = $row.find('#price').val();
                if (price == new_price && price != null ){
                    var unit = $(this).val();
                    var priceInput = $row.find('#price');
                    // Price will be handled by the backend based on unit conversions
                    priceInput.val(price);
                }
                // Calculate weight when unit changes - with debouncing
                clearTimeout($row.data('weight-debounce-timeout'));
                var timeout = setTimeout(function() {
                    calculateWeightForRow($row);
                }, 300);
                $row.data('weight-debounce-timeout', timeout);
            });
            
            // Calculate weight when amount changes - with debouncing
            $(document).on('input', '#amount', function () {
                var $row = $(this).closest('.form-row');
                clearTimeout($row.data('weight-debounce-timeout'));
                var timeout = setTimeout(function() {
                    calculateWeightForRow($row);
                }, 300);
                $row.data('weight-debounce-timeout', timeout);
            });
            
            // Function to calculate weight for a specific row - optimized
            function calculateWeightForRow($row) {
                var commodityId = $row.find('#commodity_id').val();
                var unitId = $row.find('#unit_id').val();
                var amount = parseFloat($row.find('#amount').val());
                var weightInput = $row.find('#weight');
                
                // Get or create row ID
                var rowId = $row.attr('data-row-id') || 'row_' + Date.now();
                $row.attr('data-row-id', rowId);
                
                // Cancel previous AJAX request if exists
                var previousRequest = $row.data('weight-ajax-request');
                if (previousRequest && previousRequest.readyState !== 4) {
                    previousRequest.abort();
                }
                
                // Clear loading timeout if exists
                var loadingTimeout = $row.data('weight-loading-timeout');
                if (loadingTimeout) {
                    clearTimeout(loadingTimeout);
                    $row.removeData('weight-loading-timeout');
                }
                
                // Validate inputs before calculation
                if (!commodityId || !unitId || !amount || amount <= 0 || isNaN(amount)) {
                    weightInput.val('').removeClass('weight-calculating').data('weight-value', 0);
                    updateTotalWeight();
                    return;
                }
                
                // Get cached commodity data
                var commodityData = commodityDataCache[rowId];
                
                // Try client-side calculation for simple cases (main unit with weight_per_unit)
                if (commodityData && commodityData.unit_id == unitId && commodityData.weight_per_unit) {
                    var calculatedWeight = amount * commodityData.weight_per_unit;
                    var formattedWeight = Math.round(calculatedWeight).toLocaleString('fa-IR') + ' کیلوگرم';
                    weightInput.val(formattedWeight).removeClass('weight-calculating').data('weight-value', calculatedWeight);
                    updateTotalWeight();
                    return; // Skip AJAX call
                }
                
                // For cases requiring unit conversion, use AJAX
                // Show loading state only after delay (200ms)
                var loadingTimeoutId = setTimeout(function() {
                    if ($row.attr('data-row-id') === rowId && weightInput.val() !== '') {
                        // Keep previous value visible, just add loading indicator
                        weightInput.addClass('weight-calculating');
                    } else if ($row.attr('data-row-id') === rowId) {
                        weightInput.val('در حال محاسبه...').addClass('weight-calculating');
                    }
                }, 200);
                $row.data('weight-loading-timeout', loadingTimeoutId);
                
                // Make AJAX request
                var ajaxRequest = $.ajax({
                    url: '/order/calculate-weight/' + commodityId + '/' + amount + '/' + unitId,
                    type: 'get',
                    dataType: 'json',
                    timeout: 5000,
                    success: function (response) {
                        // Clear loading timeout
                        if (loadingTimeoutId) clearTimeout(loadingTimeoutId);
                        
                        // Only update if this is still the current row
                        if ($row.attr('data-row-id') === rowId) {
                            weightInput.removeClass('weight-calculating');
                            if (response.success && response.weight !== null) {
                                weightInput.val(response.weight_formatted);
                                weightInput.data('weight-value', response.weight);
                            } else {
                                var errorMsg = response.weight_formatted || 'خطا در محاسبه';
                                weightInput.val(errorMsg);
                                weightInput.data('weight-value', 0);
                            }
                            updateTotalWeight();
                        }
                    },
                    error: function (xhr, status, error) {
                        // Clear loading timeout
                        if (loadingTimeoutId) clearTimeout(loadingTimeoutId);
                        
                        // Only update if this is still the current row
                        if ($row.attr('data-row-id') === rowId) {
                            weightInput.removeClass('weight-calculating');
                            if (status === 'abort') {
                                // Request was cancelled, don't show error
                                return;
                            }
                            weightInput.val('خطا در محاسبه');
                            weightInput.data('weight-value', 0);
                            updateTotalWeight();
                        }
                    }
                });
                
                // Store request reference for cancellation
                $row.data('weight-ajax-request', ajaxRequest);
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
                    <div class="col-12 mb-2">
                        <div class="d-flex align-items-center">
                            <input type="text"
                                   class="form-control form-control-sm flex-grow-1 commodity-search-input"
                                   placeholder="جستجو در نام کالا (مثلاً روغن موتور)">
                            <button type="button"
                                    class="btn btn-primary btn-sm btn-commodity-search ml-2">
                                جستجو
                            </button>
                        </div>
                        <small class="form-text text-muted commodity-help-text mt-1">
                            ابتدا نام کالا را جستجو کرده و سپس از لیست بالا انتخاب کنید.
                        </small>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
                        <select id="commodity_id" class="form-control form-control-sm"
                                style="max-height: 150px; overflow-y: auto;"
                                name="commodity_id[${index}]" required>
                            <option value="">انتخاب کنید...</option>
                            @foreach ($commodities as $commodity)
                                <option value="{{ $commodity->id }}"
                                        @if($commodity->discount_percentage !== null) data-discount="{{ $commodity->discount_percentage }}" @endif>
                                    {{ $commodity->title }}
                                </option>
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
                        <label for="discount_percentage">درصد تخفیف</label>
                        <input type="number" id="discount_percentage" name="discount_percentage[${index}]" 
                               class="form-control discount-input" min="0" max="100" step="1"
                               autocomplete="off" placeholder="مثال: 10">
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
