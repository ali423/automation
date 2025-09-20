{{-- Form fields partial for commodity create/edit --}}
{{-- Row 1: Basic Information --}}
<div class="form-row">
    <div class="form-group col-md-4">
        <label for="title"> {{ __('fields.title') }}</label>
        <input type="text" name="title" value="{{ old('title', $commodity->title ?? '') }}" class="form-control"
               id="title" placeholder="عنوان کالا" required="">
        <div class="invalid-feedback">
            لطفاً عنوان کالا را وارد کنید.
        </div>
    </div>
    <div class="form-group col-md-3">
        <label for="type"> {{ __('fields.type') }}</label>
        <select id="type" class="form-control" name="type" {{ isset($commodity) ? 'disabled' : 'required' }}>
            <option value="">انتخاب کنید...</option>
            <option value="material" {{ old('type', $commodity->type ?? '') == 'material' ? 'selected' : '' }}>ماده اولیه</option>
            <option value="product" {{ old('type', $commodity->type ?? '') == 'product' ? 'selected' : '' }}>فرآورده</option>
        </select>
        @if(isset($commodity))
            <input type="hidden" name="type" value="{{ $commodity->type }}">
        @endif
        <div class="invalid-feedback">نوع کالا را انتخاب کنید</div>
    </div>
    <div class="form-group col-md-3">
        <label for="unit"> {{ __('fields.unit') }}</label>
        <select id="unit" class="form-control" name="unit_id" required>
            <option value="">انتخاب کنید...</option>
            @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ old('unit_id', $commodity->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->name }} ({{ $unit->symbol }})
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback">واحد را انتخاب کنید</div>
    </div>
</div>

{{-- Row 2: Physical Properties and Pricing --}}
<div class="form-row">
    <div id="weight_per_unit_group" class="form-group col-md-3" style="display: {{ (old('type', $commodity->type ?? '') == 'product') ? 'block' : 'none' }};">
        <label for="weight_per_unit">وزن هر واحد (کیلوگرم)</label>
        <input type="number" step="0.001" name="weight_per_unit" value="{{ old('weight_per_unit', $commodity->weight_per_unit ?? '') }}" class="form-control"
               id="weight_per_unit" placeholder="مثال: 0.5" min="0.001">
        <div class="invalid-feedback">لطفاً وزن هر واحد را وارد کنید</div>
    </div>
    <div class="form-group col-md-3">
        <label for="warning_limit"> {{ __('fields.warning_limit') }} <span class="unit_label">({{ $commodity->unit->symbol ?? 'واحد' }})</span></label>
        <input type="number" step="0.01" name="warning_limit"
               value="{{ old('warning_limit', $commodity->warning_limit ?? '') }}"
               class="form-control" placeholder="{{ __('fields.warning_limit') }}" required>
        <div class="invalid-feedback">{{ __('fields.warning_limit') }} را وارد کنید</div>
    </div>
    <div id="profit_margin" class="form-group col-md-3">
        <label for="profit_margin">درصد سود (%)</label>
        <input type="number" step="0.01" min="0" max="100" name="profit_margin"
               value="{{ old('profit_margin', $commodity->profit_margin ?? '') }}"
               class="form-control" placeholder="درصد سود">
        <div class="invalid-feedback">درصد سود را وارد کنید</div>
    </div>
    <div id="purchase_price" class="form-group col-md-3">
        <label for="purchase_price"> {{ __('fields.purchase_price') }} هر <span class="unit_label2">{{ $commodity->unit->symbol ?? 'واحد' }}</span> (ریال)</label>
        <input type="number" step="0.01" min="100" name="purchase_price"
               value="{{ old('purchase_price', $commodity->purchase_price ?? '') }}" class="form-control"
               placeholder="{{ __('fields.purchase_price') }}" required>
        <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
    </div>
</div>

{{-- Row 3: Product-specific fields --}}
<div class="form-row">
    <div class="form-group col-md-3" id="pieces_per_box_group" style="display: {{ (old('type', $commodity->type ?? '') == 'product') ? 'block' : 'none' }};">
        <label for="pieces_per_box">تعداد در کارتن</label>
        <input type="number" name="pieces_per_box" value="{{ old('pieces_per_box', $commodity->pieces_per_box ?? 1) }}" class="form-control"
               id="pieces_per_box" min="1" placeholder="مثال: 24" required="">
        <div class="invalid-feedback">لطفاً تعداد در کارتن را وارد کنید</div>
    </div>
    <div class="form-group col-md-5" id="product_identifier_group" style="display: {{ (old('type', $commodity->type ?? '') == 'product') ? 'block' : 'none' }};">
        <label for="product_identifier">شناسه کالا</label>
        <input type="text" name="product_identifier" value="{{ old('product_identifier', $commodity->product_identifier ?? '') }}" class="form-control"
               id="product_identifier" placeholder="شناسه کالا" required="">
        <div class="invalid-feedback">لطفاً شناسه کالا را وارد کنید</div>
    </div>
</div>
