{{-- Commodity details partial for show view --}}
<!-- Basic Information -->
<div class="form-row">
    <div class="form-group col-md-6">
        <label>{{ __('fields.commodity.number') }}</label>
        <input type="text" value="{{ $commodity->number }}" class="form-control" disabled>
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('fields.title') }}</label>
        <input type="text" value="{{ $commodity->title }}" class="form-control" disabled>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>{{ __('fields.type') }}</label>
        <input type="text" value="{{ __('fields.commodity.types')[$commodity->type] }}" class="form-control" disabled>
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('fields.unit') }}</label>
        <input type="text" value="{{ $commodity->unit ? $commodity->unit->name . ' (' . $commodity->unit->symbol . ')' : '-' }}" class="form-control" disabled>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>وزن هر واحد (کیلوگرم)</label>
        <input type="text" value="{{ $commodity->weight_per_unit ? number_format($commodity->weight_per_unit, 3) . ' کیلوگرم' : 'تعریف نشده' }}" class="form-control" disabled>
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('fields.creator') }}</label>
        <input type="text" value="{{ isset($commodity->creator_user) ? $commodity->creator_user->full_name : 'سیستم' }}" class="form-control" disabled>
    </div>
</div>

@if($commodity->type == 'product' && $commodity->product_identifier)
<div class="form-row">
    <div class="form-group col-md-6">
        <label>شناسه کالا</label>
        <input type="text" value="{{ $commodity->product_identifier }}" class="form-control" disabled>
    </div>
    <div class="form-group col-md-6">
        <label>تعداد در کارتن</label>
        <input type="text" value="{{ $commodity->pieces_per_box ?? 1 }}" class="form-control" disabled>
    </div>
</div>
@endif

<div class="form-row">
    <div class="form-group col-md-6">
        <label>{{ __('fields.warning_limit') }} ({{ $commodity->unit ? $commodity->unit->symbol : '' }})</label>
        <input type="text" value="{{ number_format($commodity->warning_limit) }}" class="form-control" disabled>
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('fields.created_at') }}</label>
        <input type="text" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($commodity->created_at)) }}" class="form-control" disabled>
    </div>
</div>

<!-- Financial Information -->
<div class="form-row">
    @if($commodity->type == 'product')
    <div class="form-group col-md-6">
        <label>{{ __('fields.sales_price') }} (ریال)</label>
        <input type="text" value="{{ $commodity->sales_price ? number_format($commodity->sales_price) : 'تعریف نشده' }}" class="form-control" disabled>
    </div>
    @else
    <div class="form-group col-md-6">
        <label>{{ __('fields.purchase_price') }} (ریال)</label>
        <input type="text" value="{{ $commodity->purchase_price ? number_format($commodity->purchase_price) : 'تعریف نشده' }}" class="form-control" disabled>
    </div>
    @endif
</div>

@if($commodity->type == 'product')
    <div id="product_formul" class="col-lg-12">
        <p>فرمول ساخت محصول (مقادیر بر اساس واحد: <span class="text-white font-weight-bold">{{ $commodity->unit ? $commodity->unit->name . ' (' . $commodity->unit->symbol . ')' : '' }}</span>)</p>
        <div id="inputFormRow" class="form-row shadow p-4 mb-3">
            @foreach ($materials as $material)
                <div class="form-group col-md-5">
                    <label>{{ __('fields.commodity.material_type') }}</label>
                    <input type="text" value="{{ $material->title }}" class="form-control" disabled>
                </div>
                <div class="form-group col-md-3">
                    <label>{{ __('fields.commodity.material_amount') }}</label>
                    <input type="number" step="0.01" value="{{ $material->pivot->amount }}" class="form-control" disabled>
                </div>
                <div class="form-group col-md-2">
                    <label>{{ __('fields.unit') }}</label>
                    <input type="text" value="{{ $material->unit ? $material->unit->name . ' (' . $material->unit->symbol . ')' : ($commodity->unit ? $commodity->unit->name . ' (' . $commodity->unit->symbol . ')' : 'نامشخص') }}" class="form-control" disabled>
                </div>
            @endforeach
        </div>
    </div>
@endif
