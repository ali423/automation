{{-- Commodity Form Partial for Withdrawal Requests --}}
<div class="inputFormRow form-row shadow p-4 mb-3" style="position: relative;">
    @if(!isset($showRemoveButton) || $showRemoveButton)
        <i class="removeRow ti-close" type="button" style="position: absolute; top: 10px; left: 10px; cursor: pointer; font-size: 1.5rem; color: #dc3545; z-index: 10;"></i>
    @endif
    <div class="form-group col-md-6">
        <label> {{ __('fields.commodity.name') }}</label>
        <select class="form-control commodity-select" name="commodity_id[{{ $index ?? 0 }}]" onchange="pricefunc(this)" required>
            <option value="">انتخاب کنید</option>
            @foreach ($commodities as $commodity)
                <option value="{{ $commodity->id }}" {{ (isset($selectedCommodity) && $selectedCommodity->id == $commodity->id) ? 'selected' : '' }}>
                    {{ $commodity->title }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback">محصول را انتخاب کنید.</div>
    </div>
    
    <div class="form-group col-md-6">
        <label> {{ __('fields.unit') }}</label>
        <select class="form-control unit-select" name="unit_id[{{ $index ?? 0 }}]" required>
            <option value="">انتخاب کنید...</option>
            @if(isset($selectedCommodity) && $selectedCommodity->selectable_units)
                @foreach ($selectedCommodity->selectable_units as $unit)
                    <option value="{{ $unit->id }}" {{ (isset($selectedUnit) && $selectedUnit->id == $unit->id) ? 'selected' : '' }}>
                        {{ $unit->name }} ({{ $unit->symbol }})
                    </option>
                @endforeach
            @endif
        </select>
        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
    </div>

    <div class="form-group col-md-4">
        <label> {{  __('fields.commodity.amount') }}</label>
        <input type="number" class="form-control amount-input" min="1" name="amount[{{ $index ?? 0 }}]"
               autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]" 
               value="{{ $amount ?? '' }}" required="">
        <div class="invalid-feedback">
            لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
        </div>
    </div>
    
    <div class="priceholder form-group col-md-4">
        <label> {{  __('fields.sell-price_per_unit') }}</label>
        <input type="number" class="form-control price-input" min="0" step="any" name="price[{{ $index ?? 0 }}]"
               autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}" pattern="[0-9 .]" 
               value="{{ $price ?? '' }}">
        <div class="invalid-feedback">{{ __('fields.sell-price_per_unit') }} را وارد کنید.</div>
    </div>
</div>
