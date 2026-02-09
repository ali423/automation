{{-- Commodity Display Partial for Withdrawal Request Show View --}}
<div id="inputFormRow" class="form-row shadow p-4 m-3">
    <div class="showbarrel">
        <i class="fa fa-database"></i>
        <div>
            <span>Main Unit Amount</span>
            @php
                $commodityMainUnit = $mainUnitData[$commodity->id] ?? null;
            @endphp
            <span>
                @if($commodityMainUnit && isset($commodityMainUnit['main_unit_amount']))
                    {{ number_format($commodityMainUnit['main_unit_amount'], 0) }} {{ $commodityMainUnit['main_unit_name'] . ' (' . $commodityMainUnit['main_unit_symbol'] . ')' }}
                @else
                    -
                @endif
            </span>
        </div>
    </div>
    
    <div class="form-group col-md-6">
        <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
        <input type="text" value="{{ $commodity->title }}" class="form-control" disabled>
    </div>
    
    <div class="form-group col-md-6">
        <label for="unit"> {{ __('fields.unit') }}</label>
        <input type="text"
            value="{{ $commodity->pivot->unit ? ($commodity->pivot->unit->name . ' (' . $commodity->pivot->unit->symbol . ')') : '-' }}"
            class="form-control" disabled>
    </div>

    <div class="form-group col-md-4">
        <label for="amount"> {{ __('fields.commodity.amount') }} (اصلی)</label>
        <input type="text" value="{{ number_format($commodity->pivot->amount, 0) }}" class="form-control" disabled>
    </div>
    
    @if(isset($commodity->returned_amount) && $commodity->returned_amount > 0)
        <div class="form-group col-md-4">
            <label for="returned_amount" class="text-info">برگشتی از فروش</label>
            <input type="text" value="{{ number_format($commodity->returned_amount, 0) }}" class="form-control bg-info text-white" disabled>
        </div>
        <div class="form-group col-md-4">
            <label for="net_amount" class="text-success font-weight-bold">خالص فروش</label>
            <input type="text" value="{{ number_format($commodity->net_amount, 0) }}" class="form-control bg-success text-white font-weight-bold" disabled>
        </div>
    @endif
    
    <div class="form-group col-md-4">
        <label for="weight">وزن (کیلوگرم)</label>
        @php
            $weight = calculate_weight($commodity, $commodity->pivot->amount, $commodity->pivot->unit_id);
        @endphp
        <input type="text" value="{{ $weight !== null ? number_format($weight, 0) : 'نامشخص' }}" class="form-control" disabled>
    </div>
    
    <div class="form-group col-md-4">
        <label for="pieces_per_box">تعداد در کارتن</label>
        <input type="text" value="{{ $commodity->pieces_per_box ?? 1 }}" class="form-control" disabled>
    </div>
    
    @if(isset($commodity->pivot->price))
        <div class="form-group col-md-4">
            <label for="price"> {{  __('fields.sell-price_per_unit') }}</label>
            <input type="text" value="{{ $commodity->pivot->price ? number_format($commodity->pivot->price, 0) : '-' }}" class="form-control" disabled>
        </div>
        @if(isset($commodity->returned_amount) && $commodity->returned_amount > 0)
            <div class="form-group col-md-4">
                <label for="original_total" class="text-muted">مبلغ کل اصلی</label>
                <input type="text" value="{{ number_format($commodity->pivot->amount * $commodity->pivot->price, 0) }}" class="form-control" disabled>
            </div>
            <div class="form-group col-md-4">
                <label for="return_total" class="text-info">مبلغ برگشتی</label>
                <input type="text" value="{{ number_format($commodity->returned_amount * $commodity->pivot->price, 0) }}" class="form-control bg-light" disabled>
            </div>
            <div class="form-group col-md-4">
                <label for="net_total" class="text-success font-weight-bold">مبلغ خالص</label>
                <input type="text" value="{{ number_format($commodity->net_amount * $commodity->pivot->price, 0) }}" class="form-control bg-success text-white font-weight-bold" disabled>
            </div>
        @endif
    @endif
</div>
