{{-- Commodity Display Partial for Withdrawal Request Show View --}}
<div id="inputFormRow" class="form-row shadow p-4 m-3">
    <div class="showbarrel">
        <i class="fa fa-database"></i>
        <div>
            <span>مقدار نهایی</span>
            <span>
                {{ number_format($commodity->effective_amount ?? 0, 0) }}
                {{ $commodity->effective_unit ? ($commodity->effective_unit->name . ' (' . $commodity->effective_unit->symbol . ')') : '-' }}
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
            value="{{ $commodity->effective_unit ? ($commodity->effective_unit->name . ' (' . $commodity->effective_unit->symbol . ')') : '-' }}"
            class="form-control" disabled>
    </div>

    <div class="form-group col-md-4">
        <label for="amount"> {{ __('fields.commodity.amount') }} (نهایی)</label>
        <input type="text" value="{{ number_format($commodity->effective_amount ?? 0, 0) }}" class="form-control" disabled>
    </div>
    
    <div class="form-group col-md-4">
        <label for="weight">وزن (کیلوگرم)</label>
        @php
            $weight = calculate_weight($commodity, $commodity->effective_amount ?? 0, $commodity->unit_id);
        @endphp
        <input type="text" value="{{ $weight !== null ? number_format($weight, 0) : 'نامشخص' }}" class="form-control" disabled>
    </div>
    
    <div class="form-group col-md-4">
        <label for="pieces_per_box">تعداد در کارتن</label>
        <input type="text" value="{{ $commodity->pieces_per_box ?? 1 }}" class="form-control" disabled>
    </div>
    
    @if(isset($commodity->effective_price))
        <div class="form-group col-md-4">
            <label for="price"> {{  __('fields.sell-price_per_unit') }}</label>
            <input type="text" value="{{ $commodity->effective_price ? number_format($commodity->effective_price, 0) : '-' }}" class="form-control" disabled>
        </div>
        <div class="form-group col-md-4">
            <label for="net_total" class="text-success font-weight-bold">مبلغ کل نهایی</label>
            <input type="text" value="{{ number_format(($commodity->effective_amount ?? 0) * ($commodity->effective_price ?? 0), 0) }}" class="form-control bg-success text-white font-weight-bold" disabled>
        </div>
    @endif
</div>
