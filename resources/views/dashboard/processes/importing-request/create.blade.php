@extends('layouts.main')
@section('title','درخواست خرید کالا')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ثبت درخواست خرید کالا</h4>

                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('importing-request.store') }}" class="needs-validation forms-sample" enctype="multipart/form-data" novalidate="">
                            @csrf
                            <div class="form-row m-3">
                                <div class="form-group col">
                                    <label for="seller_id">نام شرکت</label>
                                    <select id="seller_id" class="form-control" name="seller_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}">{{$seller->comp_name ?? $seller->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        فروشنده را انتخاب کنید
                                    </div>
                                </div>
                            </div>
                            <div id="product_formul" class="col-lg-12">
                                <p>اطلاعات خرید کالا</p>
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    @if(isset($attributes) && $attributes->count() > 0)
                                    <div class="col-12 mb-2 attribute-filter-container">
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
                                                            <button type="button" class="btn btn-xs btn-outline-primary row-attribute-filter" data-attribute-id="{{ $attribute->id }}" data-name="{{ $attribute->name }}" style="font-size: 11px; padding: 2px 8px; margin: 2px;">
                                                                {{ $attribute->name }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="form-group col-md-6">
                                        <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
                                        <select id="commodity_id" class="form-control commodity-select" name="commodity_id[0]" onchange="pricefunc(this)" required>
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($commodities as $commodity)
                                                <option value="{{ $commodity->id }}" data-attributes="{{ $commodity->attributes->pluck('id')->join(',') }}">{{ $commodity->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="unit"> {{ __('fields.unit') }}</label>
                                        <select id="unit" class="form-control" name="unit[0]" required>
                                            <option value="">انتخاب کنید...</option>
                                            <!-- Options will be filled by AJAX, value should be unit_id -->
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                                        <input type="number" id="amount" min="1" name="amount[0]" class="form-control"
                                               autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]"  required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                                        </div>
                                    </div>
                                    <div id="priceholder" class="form-group col-md-3 d-none">
                                        <label for="price"> {{  __('fields.purchase_unit_price') }}</label>
                                        <input type="number" id="purchase_price" min="1" name="purchase_price[0]" class="form-control"
                                               autocomplete="off" placeholder="{{  __('fields.purchase_unit_price') }}" pattern="[0-9 .]" >
                                    </div>
                                </div>

                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                            </div>

                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default" accept="image/*,.pdf,.zip,.rar">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">انتخاب فایل</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-20">
                                <label for="comment">توضیحات</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment" rows="6">{{old('comment')}}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">ثبت درخواست</button>
                            <a href="{{ route('importing-request.index') }}" class="btn btn-danger">انصراف</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')

    <script type="text/javascript">
        // Preload commodity units data
        var commodityUnitsData = {};
        @foreach($commodities as $commodity)
            commodityUnitsData[{{ $commodity->id }}] = [
                @foreach($commodity->selectable_units as $unit)
                    {
                        id: {{ $unit->id }},
                        name: '{{ $unit->name }}',
                        symbol: '{{ $unit->symbol }}',
                        display_name: '{{ $unit->name }} ({{ $unit->symbol }})'
                    }@if(!$loop->last),@endif
                @endforeach
            ];
        @endforeach

        // Preload commodity types data
        var commodityTypesData = {};
        @foreach($commodities as $commodity)
            commodityTypesData[{{ $commodity->id }}] = '{{ $commodity->type }}';
        @endforeach

        // Store original commodity options HTML
        var originalCommodityOptionsHtml = '@foreach ($commodities as $commodity)<option value="{{ $commodity->id }}" data-attributes="{{ $commodity->attributes->pluck('id')->join(',') }}">{{ $commodity->title }}</option>@endforeach';

        // Store attribute filter HTML template (collapsible version)
        @if(isset($attributes) && $attributes->count() > 0)
        var attributeFilterHtml = '<div class="col-12 mb-2 attribute-filter-container"><div class="d-inline-flex align-items-center" style="cursor: pointer;" onclick="$(this).closest(\'.attribute-filter-container\').find(\'.filter-panel\').slideToggle(200);"><i class="ti-filter toggle-row-filter" style="font-size: 12px; color: #666;"></i><small style="margin-right: 6px; font-size: 11px; color: #333;">فیلتر ویژگی</small></div><div class="filter-panel mt-2" style="display: none;"><div class="card" style="background-color: #f8f9fa; border: 1px solid #e0e0e0;"><div class="card-body p-2"><div class="d-flex justify-content-between align-items-center mb-2"><input type="text" class="form-control form-control-sm row-attribute-search" placeholder="جستجو..." style="font-size: 12px; width: 150px;"><button type="button" class="btn btn-xs btn-secondary clear-row-filters" style="font-size: 11px; padding: 2px 8px;">پاک کردن</button></div><div class="d-flex flex-wrap gap-1 row-attribute-filters" style="max-height: 120px; overflow-y: auto;">@foreach($attributes as $attribute)<button type="button" class="btn btn-xs btn-outline-primary row-attribute-filter" data-attribute-id="{{ $attribute->id }}" data-name="{{ $attribute->name }}" style="font-size: 11px; padding: 2px 8px; margin: 2px;">{{ $attribute->name }}</button>@endforeach</div></div></div></div></div>';
        @else
        var attributeFilterHtml = '';
        @endif

        // add row
        $("#addRow").click(function() {
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3">' + attributeFilterHtml + '<div class="form-group col-md-6"> <label for="commodity_id"> {{ __('fields.commodity.name') }}</label> <select id="commodity_id" class="form-control commodity-select" name="commodity_id[]"  onchange="pricefunc(this)" required> <option value="">انتخاب کنید</option>' + originalCommodityOptionsHtml + '</select> <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div> </div> <div class="form-group col-md-6"> <label for="unit"> {{ __('fields.unit') }}</label> <select id="unit" class="form-control" name="unit[]" required> <option value="">انتخاب کنید...</option></select> <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div> </div> <div class="form-group col-md-6"> <label for="amount"> {{  __('fields.commodity.amount') }}</label> <input type="number" min="1" name="amount[]" class="form-control"id="amount" autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]"  required=""> <div class="invalid-feedback">لطفاً {{  __('fields.commodity.amount') }} را وارد کنید. </div></div><div id="priceholder" class="form-group col-md-3 d-none"><label for="price"> قیمت خرید</label><input type="number" id="price" min="1" name="purchase_price[]" class="form-control" autocomplete="off" placeholder="قیمت خرید" pattern="[0-9 .]"  ><div class="invalid-feedback">لطفاً قیمت خرید را وارد کنید.</div></div> <i id="removeRow" type="submit" class="ti-close"></i></div></div>';
            $('#newRow').append(html);
            document.querySelectorAll('#inputFormRow').forEach((element,index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id['+index+']');
                element.querySelector('#unit').setAttribute('name', 'unit['+index+']');
                element.querySelector('#amount').setAttribute('name', 'amount['+index+']');
                element.querySelector('#price').setAttribute('name', 'purchase_price['+index+']');
            });
        });

        // remove row
        $(document).on('click', '#removeRow', function() {
            $(this).closest('#inputFormRow').remove();
            document.querySelectorAll('#inputFormRow').forEach((element,index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id['+index+']');
                element.querySelector('#unit').setAttribute('name', 'unit['+index+']');
                element.querySelector('#amount').setAttribute('name', 'amount['+index+']');
                element.querySelector('#price').setAttribute('name', 'purchase_price['+index+']');
            });
        });

        function pricefunc(el) {
            var holder = el.closest('#inputFormRow').querySelector('#priceholder');
            var unitSelect = el.closest('#inputFormRow').querySelector('#unit');
            var id = el.value;

            // Clear unit options first
            unitSelect.innerHTML = '<option value="">انتخاب کنید...</option>';

            if (!id) {
                return;
            }

            // Get commodity type from preloaded data
            var type = commodityTypesData[id];
            if (type === "material") {
                holder.classList.remove('d-none');
            } else {
                if (!(holder.classList.contains('d-none'))) {
                    holder.classList.add('d-none');
                }
            }

            // Get selectable units from preloaded data
            var units = commodityUnitsData[id];
            if (units) {
                units.forEach(function(unit) {
                    var option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = unit.display_name;
                    unitSelect.appendChild(option);
                });
            }
        }

        // Per-row attribute filter click handler
        $(document).on('click', '.row-attribute-filter', function() {
            const $btn = $(this);
            const $row = $btn.closest('#inputFormRow');

            // Toggle button state
            if ($btn.hasClass('btn-primary')) {
                $btn.removeClass('btn-primary').addClass('btn-outline-primary');
            } else {
                $btn.removeClass('btn-outline-primary').addClass('btn-primary');
            }

            // Apply filter to this row's commodity select
            applyRowFilter($row);
            updateFilterBadge($row);
        });

        // Per-row clear filters button
        $(document).on('click', '.clear-row-filters', function() {
            const $row = $(this).closest('#inputFormRow');
            $row.find('.row-attribute-filter').removeClass('btn-primary').addClass('btn-outline-primary');
            applyRowFilter($row);
            updateFilterBadge($row);
        });

        // Per-row search in attributes
        $(document).on('input', '.row-attribute-search', function() {
            const $row = $(this).closest('#inputFormRow');
            const q = $(this).val().toString().trim().toLowerCase();
            $row.find('.row-attribute-filter').each(function() {
                const name = $(this).data('name').toString().toLowerCase();
                $(this).toggle(name.includes(q));
            });
        });

        // Update filter indicator
        function updateFilterBadge($row) {
            const count = $row.find('.row-attribute-filter.btn-primary').length;
            const $icon = $row.find('.toggle-row-filter');
            if (count > 0) {
                $icon.css('color', '#007bff');
            } else {
                $icon.css('color', '#666');
            }
        }

        // Apply filter to a specific row's commodity select
        function applyRowFilter($row) {
            const $select = $row.find('.commodity-select');
            const currentValue = $select.val();

            // Store original options if not already stored
            if (!$select.data('original-options')) {
                $select.data('original-options', $select.html());
            }

            // Restore original options
            $select.html($select.data('original-options'));

            // Get selected attributes for this row
            const selectedAttributes = [];
            $row.find('.row-attribute-filter.btn-primary').each(function() {
                selectedAttributes.push($(this).data('attribute-id').toString());
            });

            // If no filters selected, keep all options
            if (selectedAttributes.length === 0) {
                return;
            }

            // Filter options based on selected attributes
            $select.find('option').each(function() {
                const $option = $(this);
                const optionValue = $option.val();

                if (!optionValue) {
                    return; // Keep the empty "انتخاب کنید" option
                }

                const attrs = ($option.data('attributes') || '').toString().split(',').filter(Boolean);
                const hasAll = selectedAttributes.every(attrId => attrs.includes(attrId));

                if (!hasAll) {
                    $option.remove();
                }
            });

            // If current selection is no longer available, reset it
            if (currentValue && $select.find('option[value="' + currentValue + '"]').length === 0) {
                $select.val('');
                pricefunc($select[0]);
            }
        }
    </script>
    <!-- These plugins only need for the run this page -->

    <script src="{{ asset('js/default-assets/active.js') }}"></script>

    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{asset('js/default-assets/file-upload.js')}}"></script>


@endsection

