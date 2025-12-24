<div id="inputFormRow" class="form-row shadow p-4 mb-3"
     @if(isset($item) && $item->commodity)
         data-commodity-unit-id="{{ $item->commodity->unit_id }}"
         data-commodity-weight-per-unit="{{ $item->commodity->weight_per_unit ?? '' }}"
     @endif>
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
        <select id="commodity_id" class="form-control form-control-sm"
                style="max-height: 150px; overflow-y: auto;"
                name="commodity_id[{{ $index ?? 0 }}]" required>
            @if(isset($item) && $item->commodity)
                <option value="{{ $item->commodity_id }}" selected 
                        @if($item->commodity->discount_percentage !== null) data-discount="{{ $item->commodity->discount_percentage }}" @endif
                        data-unit-id="{{ $item->commodity->unit_id }}"
                        data-weight-per-unit="{{ $item->commodity->weight_per_unit ?? '' }}">
                    {{ $item->commodity->title }}
                </option>
            @else
                <option value="">انتخاب کنید...</option>
                @foreach ($commodities as $commodity)
                    <option value="{{ $commodity->id }}"
                            @if($commodity->discount_percentage !== null) data-discount="{{ $commodity->discount_percentage }}" @endif
                            data-unit-id="{{ $commodity->unit_id }}"
                            data-weight-per-unit="{{ $commodity->weight_per_unit ?? '' }}">
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
               value="{{ isset($item) && $item->price ? number_format($item->price, 0) : '' }}" class="form-control"
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
               value="{{ isset($item) && $item->weight_kg !== null ? number_format($item->weight_kg, 0) . ' کیلوگرم' : (isset($item) ? 'وزن تعریف نشده' : '') }}">
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
