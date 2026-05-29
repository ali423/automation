@extends('layouts.main')
@section('title','ویرایش سفارش')

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/imexport-print.css')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/daterange-picker.css') }}">
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
                            <div class="form-row m-3">
                                <div class="form-group col">
                                    <label for="credit_validity_days">مدت اعتبار (روز)</label>
                                    <input type="number" name="credit_validity_days" id="credit_validity_days" class="form-control" 
                                           min="1" value="{{ $order->credit_validity_days ?? '' }}">
                                    <div class="invalid-feedback">
                                        مدت اعتبار را به روز وارد کنید
                                    </div>
                                </div>
                            </div>
                            <div id="order_formul" class="col-lg-12">
                                <p>اطلاعات سفارش</p>
                                @foreach($order->orderItems as $index => $item)
                                    @include('dashboard.order.partials.order-item-row', [
                                        'commodities' => $commodities,
                                        'attributes' => $attributes ?? null,
                                        'index' => $index,
                                        'item' => $item,
                                        'showRemove' => $order->orderItems->count() > 1,
                                        'showAllCommodityOptions' => true,
                                    ])
                                @endforeach
                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                            </div>
                            
                            <!-- Total Weight Display -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <strong>مجموع وزن سفارش:</strong> 
                                        <span id="totalWeight">{{ $order->total_weight_kg !== null ? number_format($order->total_weight_kg, 0) . ' کیلوگرم' : 'نامشخص' }}</span>
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
            // Store commodity data per row for caching
            var commodityDataCache = {};
            
            // Initialize commodity data cache from existing rows (if any) and show packaging info
            $('.form-row').each(function() {
                var $row = $(this);
                var rowId = $row.attr('data-row-id') || 'row_' + Date.now();
                $row.attr('data-row-id', rowId);
                
                var selectedOption = $row.find('#commodity_id option:selected');
                var commodityUnitId = selectedOption.data('unit-id');
                var commodityWeightPerUnit = selectedOption.data('weight-per-unit');
                var piecesPerBox = selectedOption.data('pieces-per-box');

                if (commodityUnitId) {
                    commodityDataCache[rowId] = {
                        unit_id: parseInt(commodityUnitId),
                        weight_per_unit: commodityWeightPerUnit ? parseFloat(commodityWeightPerUnit) : null,
                        pieces_per_box: piecesPerBox || null
                    };
                }

                if (piecesPerBox && Number(piecesPerBox) > 0) {
                    $row.find('#pieces_per_box_display').val(piecesPerBox);
                    $row.find('[id^="packaging_count_group_"]').show();
                    $row.find('[id^="pieces_per_box_group_"]').show();
                }
            });
            
            $(document).on('change', '#commodity_id', function () {
                var $row = $(this).closest('.form-row');
                var commodity_id = $(this).val();
                var priceInput = $row.find('#price');
                var unitSelect = $row.find('#unit_id');
                var helpText = $(this).closest('.form-group').find('.commodity-help-text');
                var weightInput = $row.find('#weight');
                var packagingInput = $row.find('#packaging_count');
                var packagingGroup = $row.find('[id^="packaging_count_group_"]');
                var piecesDisplay = $row.find('#pieces_per_box_display');
                var piecesGroup = $row.find('[id^="pieces_per_box_group_"]');

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
                
                // Clear weight, packaging display and commodity cache when commodity changes
                weightInput.val('').removeClass('weight-calculating').data('weight-value', 0);
                packagingInput.val('');
                piecesDisplay.val('-');
                packagingGroup.hide();
                piecesGroup.hide();
                delete commodityDataCache[$row.attr('data-row-id')];
                
                // Set loading states
                unitSelect.prop('disabled', true).empty().append('<option value="">در حال بارگذاری...</option>');
                priceInput.prop('disabled', true).attr('placeholder','در حال بارگذاری...');

                // Get commodity meta from selected option
                var selectedOption = $(this).find('option:selected');
                var piecesPerBox = selectedOption.data('pieces-per-box');
                var mainUnitId = selectedOption.data('unit-id');

                // Show packaging info if pieces_per_box is defined and > 0
                var rowId = $row.attr('data-row-id') || 'row_' + Date.now();
                $row.attr('data-row-id', rowId);
                if (piecesPerBox && Number(piecesPerBox) > 0) {
                    piecesDisplay.val(piecesPerBox);
                    packagingGroup.show();
                    piecesGroup.show();
                } else {
                    packagingGroup.hide();
                    piecesGroup.hide();
                }

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
                            
                            // Cache commodity data for weight calculation and packaging sync
                            commodityDataCache[rowId] = {
                                unit_id: response.commodity.unit_id,
                                weight_per_unit: response.commodity.weight_per_unit,
                                pieces_per_box: piecesPerBox || response.commodity.pieces_per_box || null
                            };
                        } else {
                            unitSelect.empty().append('<option value="">خطا در بارگذاری</option>').prop('disabled', false);
                        }
                    },
                    error: function () {
                        unitSelect.empty().append('<option value="">خطا در بارگذاری</option>').prop('disabled', false);
                    }
                });
                
                // Get commodity price (skip overwrite when re-selecting the same item on edit)
                var initialCommodityId = $row.data('initial-commodity-id');
                var isSameExistingCommodity = initialCommodityId
                    && String(commodity_id) === String(initialCommodityId);

                if (isSameExistingCommodity) {
                    priceInput.prop('disabled', false).attr('placeholder', '');
                } else {
                    $.ajax({
                        url: '/inventory-ajax/' + commodity_id,
                        type: 'get',
                        dataType: 'json',
                        success: function (response) {
                            price = (response && response.data) ? response.data.price : null;
                            var formattedPrice = price ? Math.round(parseFloat(price)).toString() : '';
                            priceInput.val(formattedPrice).attr('placeholder', '').prop('disabled', false);
                        },
                        error: function () {
                            priceInput.val('').attr('placeholder', 'نامشخص').prop('disabled', false);
                        }
                    });
                }
                
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
                    var formattedPrice = price ? Math.round(parseFloat(price)).toString() : '';
                    priceInput.val(formattedPrice);
                }
                // Sync amount based on packaging if possible
                syncAmountFromPackaging($row);
                // Calculate weight when unit changes - with debouncing
                clearTimeout($row.data('weight-debounce-timeout'));
                var timeout = setTimeout(function() {
                    calculateWeightForRow($row);
                }, 300);
                $row.data('weight-debounce-timeout', timeout);
            });
            
            // Calculate weight when amount changes - with debouncing and sync packaging
            $(document).on('input', '#amount', function () {
                var $row = $(this).closest('.form-row');
                // Update the original amount when user changes it
                $row.data('original-amount', $(this).val());
                // Sync packaging from amount when unit is main
                syncPackagingFromAmount($row);
                clearTimeout($row.data('weight-debounce-timeout'));
                var timeout = setTimeout(function() {
                    calculateWeightForRow($row);
                }, 300);
                $row.data('weight-debounce-timeout', timeout);
            });

            // Packaging count change -> sync amount
            $(document).on('input', '#packaging_count', function () {
                var $row = $(this).closest('.form-row');
                syncAmountFromPackaging($row);
                clearTimeout($row.data('weight-debounce-timeout'));
                var timeout = setTimeout(function() {
                    calculateWeightForRow($row);
                }, 300);
                $row.data('weight-debounce-timeout', timeout);
            });

            // Commodity search triggered only on button click
            $(document).on('click', '.btn-commodity-search', function () {
                var $row = $(this).closest('.form-row');
                var term = $row.find('.commodity-search-input').val().trim();
                var $select = $row.find('#commodity_id');
                var $help = $row.find('.commodity-help-text');

                // Get selected attribute filters for THIS ROW (toggle buttons)
                var selectedAttributes = [];
                $row.find('.row-attribute-filter.btn-primary').each(function() {
                    selectedAttributes.push($(this).data('attribute-id').toString());
                });

                // If search term is empty, restore original full list (if stored) and return
                if (!term && selectedAttributes.length === 0) {
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
                    data: { 
                        search: term,
                        attributes: selectedAttributes
                    },
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
                                if (item.unit_id !== null && item.unit_id !== undefined) {
                                    option.attr('data-unit-id', item.unit_id);
                                }
                                if (item.weight_per_unit !== null && item.weight_per_unit !== undefined) {
                                    option.attr('data-weight-per-unit', item.weight_per_unit);
                                }
                                if (item.pieces_per_box !== null && item.pieces_per_box !== undefined) {
                                    option.attr('data-pieces-per-box', item.pieces_per_box);
                                }
                                if (item.attributes !== null && item.attributes !== undefined) {
                                    option.attr('data-attributes', item.attributes);
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

            // Per-row attribute filter click handler
            $(document).on('click', '.row-attribute-filter', function() {
                var $btn = $(this);
                var $row = $btn.closest('#inputFormRow');

                // Toggle button state
                if ($btn.hasClass('btn-primary')) {
                    $btn.removeClass('btn-primary').addClass('btn-outline-primary');
                } else {
                    $btn.removeClass('btn-outline-primary').addClass('btn-primary');
                }

                // Apply filter to this row's commodity select
                applyRowFilter($row);
                // Update filter indicator for this row
                updateRowFilterIndicator($row);
            });

            // Per-row clear filters button
            $(document).on('click', '.clear-row-filters', function() {
                var $row = $(this).closest('#inputFormRow');
                $row.find('.row-attribute-filter').removeClass('btn-primary').addClass('btn-outline-primary');
                applyRowFilter($row);
                updateRowFilterIndicator($row);
            });

            // Per-row search in attributes
            $(document).on('input', '.row-attribute-search', function() {
                var $row = $(this).closest('#inputFormRow');
                var q = $(this).val().toString().trim().toLowerCase();
                $row.find('.row-attribute-filter').each(function() {
                    var name = $(this).data('name').toString().toLowerCase();
                    $(this).toggle(name.includes(q));
                });
            });

            // Update filter indicator (icon color) for a specific row
            function updateRowFilterIndicator($row) {
                var count = $row.find('.row-attribute-filter.btn-primary').length;
                var $icon = $row.find('.toggle-row-filter');
                if (count > 0) {
                    $icon.css('color', '#007bff');
                } else {
                    $icon.css('color', '#666');
                }
            }

            // Apply filter to a specific row's commodity select
            function applyRowFilter($row) {
                var $select = $row.find('.commodity-select');
                var currentValue = $select.val();

                // Store original options if not already stored
                if (!$select.data('original-options')) {
                    $select.data('original-options', $select.html());
                }

                // Restore original options
                $select.html($select.data('original-options'));

                // Get selected attributes for this row
                var selectedAttributes = [];
                $row.find('.row-attribute-filter.btn-primary').each(function() {
                    selectedAttributes.push($(this).data('attribute-id').toString());
                });

                // If no filters selected, keep all options
                if (selectedAttributes.length === 0) {
                    return;
                }

                // Filter options based on selected attributes
                $select.find('option').each(function() {
                    var $option = $(this);
                    var optionValue = $option.val();

                    if (!optionValue) {
                        return; // Keep the empty "انتخاب کنید" option
                    }

                    var attrs = ($option.data('attributes') || '').toString().split(',').filter(Boolean);
                    var hasAll = selectedAttributes.every(function(attrId) {
                        return attrs.includes(attrId);
                    });

                    if (!hasAll) {
                        $option.remove();
                    }
                });

                // If current selection is no longer available, reset it
                if (currentValue && $select.find('option[value="' + currentValue + '"]').length === 0) {
                    $select.val('');
                }
            }

            // Store attribute filter HTML template for new rows
            @if(isset($attributes) && $attributes->count() > 0)
            var attributeFilterHtml = `<div class="col-12 mb-2 attribute-filter-container">
                <div class="d-inline-flex align-items-center" style="cursor: pointer;" onclick="$(this).closest('.attribute-filter-container').find('.filter-panel').slideToggle(200);">
                    <i class="ti-filter toggle-row-filter" style="font-size: 12px; color: #666;"></i>
                    <small style="margin-right: 6px; font-size: 11px; color: #333;">فیلتر ویژگی</small>
                </div>
                <div class="filter-panel mt-2" style="display: none;">
                    <div class="card" style="background-color: #f8f9fa; border: 1px solid #e0e0e0;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <input type="text" class="form-control form-control-sm row-attribute-search" placeholder="جستجو..." style="font-size: 12px; width: 150px;">
                                <button type="button" class="btn btn-xs btn-secondary clear-row-filters" style="font-size: 11px; padding: 2px 8px;">پاک کردن</button>
                            </div>
                            <div class="d-flex flex-wrap gap-1 row-attribute-filters" style="max-height: 150px; overflow-y: auto; scrollbar-width: thin;">
                                @foreach($attributes as $attribute)
                                <button type="button" class="btn btn-xs btn-outline-primary row-attribute-filter" data-attribute-id="{{ $attribute->id }}" data-name="{{ $attribute->name }}" style="font-size: 11px; padding: 2px 8px; margin: 2px;">{{ $attribute->name }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
            @else
            var attributeFilterHtml = '';
            @endif

            // Add row
            $('#addRow').click(function () {
                var index = $('#order_formul .form-row').length;
                var html = `<div id="inputFormRow" class="form-row shadow p-4 mb-3">
                    ${attributeFilterHtml}
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
                        <select id="commodity_id" class="form-control form-control-sm commodity-select"
                                style="max-height: 150px; overflow-y: auto;"
                                name="commodity_id[${index}]" required>
                            <option value="">انتخاب کنید...</option>
                            @foreach ($commodities as $commodity)
                                <option value="{{ $commodity->id }}"
                                        @if($commodity->discount_percentage !== null) data-discount="{{ $commodity->discount_percentage }}" @endif
                                        data-unit-id="{{ $commodity->unit_id }}"
                                        data-weight-per-unit="{{ $commodity->weight_per_unit ?? '' }}"
                                        data-pieces-per-box="{{ $commodity->pieces_per_box ?? '' }}"
                                        data-attributes="{{ $commodity->attributes->pluck('id')->join(',') }}">
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
                    <div class="form-group col-md-2" id="packaging_count_group_${index}" style="display: none;">
                        <label for="packaging_count">تعداد بسته</label>
                        <input type="number" id="packaging_count" 
                               name="packaging_count[${index}]" 
                               class="form-control packaging-count-input" 
                               min="1" step="1"
                               autocomplete="off" 
                               placeholder="مثال: 5">
                        <small class="form-text text-muted mt-1">
                            هنگام تغییر این مقدار، تعداد واحد خودکار محاسبه می‌شود.
                        </small>
                    </div>
                    <div class="form-group col-md-2" id="pieces_per_box_group_${index}" style="display: none;">
                        <label for="pieces_per_box_display">تعداد در کارتن</label>
                        <input type="text" id="pieces_per_box_display" 
                               class="form-control pieces-per-box-display" 
                               readonly
                               placeholder="-">
                        <small class="form-text text-muted mt-1">
                            از طرف کالا تعریف شده است.
                        </small>
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
                        <input type="text" id="price" name="price[${index}]" value="" class="form-control price-input"
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
                    $(this).find('input[name^="packaging_count"]').attr('name', 'packaging_count[' + index + ']');
                    $(this).find('input[name^="commodity_amount"]').attr('name', 'commodity_amount[' + index + ']');
                    $(this).find('input[name^="price"]').attr('name', 'price[' + index + ']');
                    $(this).find('input[name^="discount_percentage"]').attr('name', 'discount_percentage[' + index + ']');
                });
            }
            
            // --- Packaging / Amount sync helpers ---
            function getRowCommodityMeta($row) {
                var rowId = $row.attr('data-row-id');
                var cache = commodityDataCache[rowId] || {};
                return {
                    piecesPerBox: cache.pieces_per_box ? Number(cache.pieces_per_box) : null,
                    mainUnitId: cache.unit_id ? Number(cache.unit_id) : null
                };
            }

            function syncAmountFromPackaging($row) {
                var packagingInput = $row.find('#packaging_count');
                var amountInput = $row.find('#amount');
                var unitSelect = $row.find('#unit_id');
                var meta = getRowCommodityMeta($row);

                var piecesPerBox = meta.piecesPerBox;
                var mainUnitId = meta.mainUnitId;
                if (!piecesPerBox || piecesPerBox <= 0) {
                    return;
                }

                var packagingVal = Number(packagingInput.val());
                var selectedUnit = Number(unitSelect.val());
                if (!packagingVal || packagingVal <= 0 || !selectedUnit) {
                    return;
                }

                // Calculate total amount in main unit
                var mainAmount = packagingVal * piecesPerBox;

                // If selected unit is main unit, we can safely set the amount
                if (selectedUnit === mainUnitId) {
                    amountInput.val(mainAmount);
                } else {
                    // Need to convert from main unit to selected unit via AJAX
                    var commodityId = $row.find('#commodity_id').val();
                    if (!commodityId) {
                        return;
                    }
                    
                    $.ajax({
                        url: '/order/convert-amount/' + commodityId + '/' + mainAmount + '/' + mainUnitId + '/' + selectedUnit,
                        type: 'get',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success && response.converted_amount !== null) {
                                amountInput.val(response.converted_amount_rounded);
                            } else {
                                // Conversion failed, fallback to main amount
                                amountInput.val(mainAmount);
                            }
                        },
                        error: function() {
                            // On error, fallback to main amount
                            amountInput.val(mainAmount);
                        }
                    });
                }
            }

            function syncPackagingFromAmount($row) {
                var packagingInput = $row.find('#packaging_count');
                var amountInput = $row.find('#amount');
                var unitSelect = $row.find('#unit_id');
                var meta = getRowCommodityMeta($row);

                var piecesPerBox = meta.piecesPerBox;
                var mainUnitId = meta.mainUnitId;
                if (!piecesPerBox || piecesPerBox <= 0) {
                    return;
                }

                var amountVal = Number(amountInput.val());
                var selectedUnit = Number(unitSelect.val());
                if (!amountVal || amountVal <= 0 || !selectedUnit) {
                    return;
                }

                // Only sync packaging when we are in the main unit (no client-side conversion)
                if (selectedUnit === mainUnitId) {
                    var packagingVal = Math.floor(amountVal / piecesPerBox);
                    if (packagingVal > 0) {
                        packagingInput.val(packagingVal);
                    }
                }
            }

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
                
                // Additional validation: check if unitId actually exists in dropdown
                var unitOption = $row.find('#unit_id option[value="' + unitId + '"]');
                if (unitOption.length === 0) {
                    // Unit doesn't exist in dropdown - don't make AJAX call
                    weightInput.val('واحد نامعتبر').removeClass('weight-calculating').data('weight-value', 0);
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
                    timeout: 10000,
                    success: function (response) {
                        // Clear loading timeout
                        if (loadingTimeoutId) clearTimeout(loadingTimeoutId);
                        
                        // Only update if this is still the current row
                        if ($row.attr('data-row-id') === rowId) {
                            weightInput.removeClass('weight-calculating');
                            if (response.success) {
                                // Backend always returns success: true, but weight_formatted may contain error message
                                if (response.weight !== null && response.weight !== undefined) {
                                    // Valid weight calculated
                                    weightInput.val(response.weight_formatted);
                                    weightInput.data('weight-value', response.weight);
                                } else {
                                    // Weight calculation failed - show the formatted message from backend
                                    var errorMsg = response.weight_formatted || 'خطا در محاسبه';
                                    weightInput.val(errorMsg);
                                    weightInput.data('weight-value', 0);
                                }
                            } else {
                                // Backend returned success: false (exception occurred)
                                var errorMsg = response.message || 'خطا در محاسبه';
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
                            // Network error or timeout
                            if (status === 'timeout') {
                                weightInput.val('خطا: زمان محاسبه به پایان رسید');
                            } else {
                                weightInput.val('خطا در اتصال به سرور');
                            }
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
                    $('#order_formul .form-row').each(function() {
                        var weightValue = $(this).find('#weight').data('weight-value');
                        if (weightValue && !isNaN(weightValue)) {
                            totalWeight += parseFloat(weightValue);
                        }
                    });
                    $('#totalWeight').text(totalWeight.toFixed(0) + ' کیلوگرم');
                }, 100); // Small debounce for total weight updates
            }
            
            // Renumber indices before form submission to ensure all items are included
            $('#orderEditForm').on('submit', function(e) {
                renumberFormIndices();
            });
            
            // Initialize page: renumber form indices and populate units for existing items
            $('.usage').first().persianDatepicker();
            renumberFormIndices();
            
            // Calculate initial total weight from existing data-weight-value attributes
            // This ensures the total is correct even before AJAX calls complete
            updateTotalWeight();
            
            // Populate unit dropdowns for existing order items when page loads
            var rowsToProcess = $('#order_formul .form-row').length;
            var rowsProcessed = 0;
            
            $('#order_formul .form-row').each(function() {
                var $row = $(this);
                var commoditySelect = $row.find('select[name^="commodity_id"]');
                var unitSelect = $row.find('select[name^="unit_id"]');
                var commodityId = commoditySelect.val();
                var currentUnitId = unitSelect.attr('data-selected-unit');
                
                // Store original amount to detect changes
                var originalAmount = $row.find('#amount').val();
                $row.data('original-amount', originalAmount);
                
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
                                    // Verify the value was set (it might fail if unitId doesn't exist in options)
                                    var actualUnitId = unitSelect.val();
                                    if (actualUnitId && actualUnitId === currentUnitId) {
                                        // Check if we already have a valid weight value from server
                                        var existingWeight = $row.find('#weight').data('weight-value');
                                        
                                        // Only recalculate on page load if weight is missing/invalid
                                        // This prevents unnecessary AJAX calls on page load when we already have valid weights
                                        // User changes will trigger recalculation via event handlers (input/change events)
                                        if (!existingWeight || existingWeight === 0 || isNaN(existingWeight)) {
                                            // Weight is missing or invalid - recalculate it
                                            calculateWeightForRow($row);
                                        }
                                        // Otherwise, keep existing weight value from data-weight-value (no AJAX call needed)
                                    } else {
                                        // Unit couldn't be set - maybe it's not in the available units list
                                        // Don't calculate weight, keep existing weight value from data-weight-value
                                    }
                                } else {
                                    // No unit selected, don't calculate weight
                                }
                            }
                            rowsProcessed++;
                            if (rowsProcessed === rowsToProcess) {
                                // All rows processed, update total weight
                                setTimeout(function() {
                                    updateTotalWeight();
                                }, 300);
                            }
                        },
                        error: function(xhr, status, error) {
                            // Silent error handling - units will remain empty
                            rowsProcessed++;
                            if (rowsProcessed === rowsToProcess) {
                                // All rows processed, update total weight
                                setTimeout(function() {
                                    updateTotalWeight();
                                }, 300);
                            }
                        }
                    });
                } else {
                    // If no commodity is selected, still try to calculate weight if unit is set
                    var currentUnitId = unitSelect.attr('data-selected-unit');
                    if (currentUnitId) {
                        calculateWeightForRow($row);
                    }
                    rowsProcessed++;
                    if (rowsProcessed === rowsToProcess) {
                        // All rows processed, update total weight
                        setTimeout(function() {
                            updateTotalWeight();
                        }, 300);
                    }
                }
            });
            
            // If no rows to process, initialize total weight immediately
            if (rowsToProcess === 0) {
                updateTotalWeight();
            }
            
            // Format price input with thousand separators
            $(document).on('input', '.price-input', function() {
                let value = $(this).val();
                // Remove all commas
                value = value.replace(/,/g, '');
                // Keep only digits
                value = value.replace(/[^\d]/g, '');
                if (value) {
                    // Add thousand separators
                    value = parseInt(value).toLocaleString('en-US');
                }
                $(this).val(value);
            });
            
            // Remove commas before form submission
            $('#orderEditForm').on('submit', function() {
                $('.price-input').each(function() {
                    let value = $(this).val();
                    value = value.replace(/,/g, '');
                    $(this).val(value);
                });
            });
        });
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/daterange-picker.js') }}"></script>
@endsection

