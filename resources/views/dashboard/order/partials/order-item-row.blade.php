<div id="inputFormRow" class="form-row shadow p-4 mb-3">
    <div class="form-group col-md-3">
        <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
        <select id="commodity_id" class="form-control" name="commodity_id[{{ $index ?? 0 }}]" required>
            <option value="">انتخاب کنید</option>
            @foreach ($commodities as $commodity)
                <option value="{{ $commodity->id }}"
                    @if(isset($item) && $item->commodity_id == $commodity->id) selected @endif
                >{{$commodity->title}}</option>
            @endforeach
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
               value="{{ isset($item) ? $item->price : '' }}" class="form-control"
               autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}">
    </div>
    <div class="form-group col-md-2">
        <label for="weight">وزن (کیلوگرم)</label>
        <input type="text" id="weight" class="form-control" readonly
               placeholder="وزن محاسبه می‌شود..." 
               value="{{ isset($item) && $item->weight_kg !== null ? number_format($item->weight_kg, 3) . ' کیلوگرم' : (isset($item) ? 'وزن تعریف نشده' : '') }}">
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
