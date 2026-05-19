<div id="inputFormRow" class="form-row shadow p-4 mb-3"
     @if(isset($item) && $item->commodity)
         data-commodity-unit-id="{{ $item->commodity->unit_id }}"
         data-commodity-weight-per-unit="{{ $item->commodity->weight_per_unit ?? '' }}"
         data-initial-commodity-id="{{ $item->commodity_id }}"
     @endif>
    
    {{-- Attribute Filters (per row) --}}
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
    
    {{-- Row 1: search box (full width) --}}
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
            @if(isset($item) && $item->commodity)
                کالای انتخاب شده: {{ $item->commodity->title }}
            @else
                ابتدا نام کالا را جستجو کرده و سپس از لیست بالا انتخاب کنید.
            @endif
        </small>
    </div>

    {{-- Row 2: commodity + other fields --}}
    <div class="form-group col-md-3">
        <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
        <select id="commodity_id" class="form-control form-control-sm commodity-select"
                style="max-height: 150px; overflow-y: auto;"
                name="commodity_id[{{ $index ?? 0 }}]" required>
            @if(!empty($showAllCommodityOptions))
                <option value="">انتخاب کنید...</option>
                @foreach ($commodities as $commodity)
                    <option value="{{ $commodity->id }}"
                            @if(isset($item) && (int) $item->commodity_id === (int) $commodity->id) selected @endif
                            @if($commodity->discount_percentage !== null) data-discount="{{ $commodity->discount_percentage }}" @endif
                            data-unit-id="{{ $commodity->unit_id }}"
                            data-weight-per-unit="{{ $commodity->weight_per_unit ?? '' }}"
                            data-pieces-per-box="{{ $commodity->pieces_per_box ?? '' }}"
                            data-attributes="{{ $commodity->attributes->pluck('id')->join(',') }}">
                        {{ $commodity->title }}
                    </option>
                @endforeach
            @elseif(isset($item) && $item->commodity)
                <option value="{{ $item->commodity_id }}" selected 
                        @if($item->commodity->discount_percentage !== null) data-discount="{{ $item->commodity->discount_percentage }}" @endif
                        data-unit-id="{{ $item->commodity->unit_id }}"
                        data-weight-per-unit="{{ $item->commodity->weight_per_unit ?? '' }}"
                        data-pieces-per-box="{{ $item->commodity->pieces_per_box ?? '' }}"
                        data-attributes="{{ $item->commodity->attributes->pluck('id')->join(',') }}">
                    {{ $item->commodity->title }}
                </option>
            @else
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
            @endif
        </select>
        <div class="invalid-feedback">
            {{ __('fields.commodity.name')}} را انتخاب کنید
        </div>
    </div>
    <div class="form-group col-md-2">
        <label for="unit_id"> {{ __('fields.unit') }}</label>
        <select id="unit_id" class="form-control" name="unit_id[{{ $index ?? 0 }}]" 
                @if(isset($item)) data-selected-unit="{{ $item->unit_id }}" @endif required>
            <option value="">انتخاب کنید...</option>
        </select>
        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
    </div>
    {{-- Packaging Count Field --}}
    <div class="form-group col-md-2" id="packaging_count_group_{{ $index ?? 0 }}" style="display: none;">
        <label for="packaging_count">تعداد بسته</label>
        <input type="number" id="packaging_count" 
               name="packaging_count[{{ $index ?? 0 }}]" 
               class="form-control packaging-count-input" 
               min="1" step="1"
               autocomplete="off" 
               placeholder="مثال: 5"
               value="{{ isset($item) && isset($item->packaging_count) ? $item->packaging_count : '' }}">
        <small class="form-text text-muted mt-1">
            هنگام تغییر این مقدار، تعداد واحد خودکار محاسبه می‌شود.
        </small>
    </div>
    {{-- Pieces Per Box Display Field --}}
    <div class="form-group col-md-2" id="pieces_per_box_group_{{ $index ?? 0 }}" style="display: none;">
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
        <input type="number" id="amount" min="1" name="commodity_amount[{{ $index ?? 0 }}]" class="form-control"
               autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}"
               pattern="[0-9 .]" value="{{ isset($item) ? $item->commodity_amount : '' }}" required="">
        <div class="invalid-feedback">
            لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
        </div>
    </div>
    <div class="form-group col-md-2">
        <label for="price"> {{  __('fields.sell-price_per_unit') }}</label>
        <input type="text" id="price" name="price[{{ $index ?? 0 }}]" 
               value="{{ isset($item) && $item->price ? number_format($item->original_price, 0) : '' }}" class="form-control price-input"
               autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}">
    </div>
    <div class="form-group col-md-2">
        <label for="discount_percentage">درصد تخفیف</label>
        <input type="number" id="discount_percentage" name="discount_percentage[{{ $index ?? 0 }}]" 
               value="{{ isset($item) ? $item->discount_percentage : '' }}" 
               class="form-control discount-input" min="0" max="100" step="1"
               autocomplete="off" placeholder="مثال: 10">
    </div>
    <div class="form-group col-md-2">
        <label for="weight">وزن (کیلوگرم)</label>
        <input type="text" id="weight" class="form-control" readonly
               placeholder="وزن محاسبه می‌شود..." 
               value="{{ isset($item) && $item->weight_kg !== null ? number_format($item->weight_kg, 0) . ' کیلوگرم' : (isset($item) ? 'وزن تعریف نشده' : '') }}"
               @if(isset($item))
               data-weight-value="{{ $item->weight_kg !== null ? $item->weight_kg : 0 }}"
               @endif>
    </div>
    @if(isset($showRemove) && $showRemove)
        <div class="form-group col-md-1">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-danger btn-sm remove-row">
                <i class="ti-close"></i>
            </button>
        </div>
    @endif
</div>
