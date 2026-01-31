{{-- Form fields partial for commodity create/edit --}}
@php
    // Ensure $commodity is a valid object
    if (!is_object($commodity)) {
        $commodity = new \App\Models\Commodity();
    }
@endphp
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
        <select id="type" class="form-control" name="type" {{ $commodity->id ? 'disabled' : 'required' }}>
            <option value="">انتخاب کنید...</option>
            <option value="material" {{ old('type', $commodity->type ?? '') == 'material' ? 'selected' : '' }}>ماده اولیه</option>
            <option value="product" {{ old('type', $commodity->type ?? '') == 'product' ? 'selected' : '' }}>فرآورده</option>
        </select>
        @if($commodity->id)
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
        <input type="number" step="any" name="weight_per_unit" value="{{ old('weight_per_unit', $commodity->weight_per_unit ?? '') }}" class="form-control"
               id="weight_per_unit" placeholder="مثال: 5.5" min="0.001" required>
        <div class="invalid-feedback">لطفاً وزن هر واحد را وارد کنید</div>
    </div>
    <div id="litrage_group" class="form-group col-md-3" style="display: {{ (old('type', $commodity->type ?? '') == 'product') ? 'block' : 'none' }};">
        <label for="litrage">حجم هر واحد (لیتر)</label>
        <input type="number" step="any" name="litrage" value="{{ old('litrage', $commodity->litrage ?? '') }}" class="form-control"
               id="litrage" placeholder="مثال: 1.5" min="0">
        <div class="invalid-feedback">لطفاً حجم هر واحد را وارد کنید</div>
    </div>
    <div class="form-group col-md-3">
        <label for="warning_limit"> {{ __('fields.warning_limit') }} <span class="unit_label">({{ $commodity->unit?->symbol ?? 'واحد' }})</span></label>
        <input type="number" step="0.01" name="warning_limit"
               value="{{ old('warning_limit', $commodity->warning_limit ?? '') }}"
               class="form-control" placeholder="{{ __('fields.warning_limit') }}" required>
        <div class="invalid-feedback">{{ __('fields.warning_limit') }} را وارد کنید</div>
    </div>
    <div id="sales_price" class="form-group col-md-3" style="display: {{ (old('type', $commodity->type ?? '') == 'product') ? 'block' : 'none' }};">
        <label for="sales_price">{{ __('fields.sales_price') }} (ریال)</label>
        <input type="number" step="1" min="0" name="sales_price"
               value="{{ old('sales_price', $commodity->sales_price ? number_format($commodity->sales_price, 0, '.', '') : '') }}"
               class="form-control" placeholder="{{ __('fields.sales_price') }}">
        <div class="invalid-feedback">{{ __('fields.sales_price') }} را وارد کنید</div>
    </div>
    <div id="purchase_price" class="form-group col-md-3" style="display: {{ (old('type', $commodity->type ?? '') == 'material') ? 'block' : 'none' }};">
        <label for="purchase_price"> {{ __('fields.purchase_price') }} هر <span class="unit_label2">{{ $commodity->unit?->symbol ?? 'واحد' }}</span> (ریال)</label>
        <input type="number" step="0.01" min="100" name="purchase_price"
               value="{{ old('purchase_price', $commodity->purchase_price ?? '') }}" class="form-control"
               placeholder="{{ __('fields.purchase_price') }}" required>
        <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
    </div>
</div>

{{-- Row 3: Product-specific fields and Discount --}}
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
    <div id="discount_percentage_group" class="form-group col-md-4" style="display: {{ (old('type', $commodity->type ?? '') == 'product') ? 'block' : 'none' }};">
        <label for="discount_percentage">درصد تخفیف پیش‌فرض</label>
        <input type="number" step="1" min="0" max="100" name="discount_percentage"
               value="{{ old('discount_percentage', $commodity->discount_percentage ?? '') }}"
               class="form-control" placeholder="مثال: 10 برای 10%">
        <div class="invalid-feedback">درصد تخفیف باید بین 0 تا 100 باشد</div>
    </div>
</div>
{{-- Row 4: Attributes --}}
<div class="form-row">
    <div class="form-group col-md-12">
        <label>ویژگی‌ها</label>
        @if($attributes && $attributes->count() > 0)
            <div class="card" style="border: 1px solid #e0e0e0;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <input type="text" class="form-control form-control-sm" id="attribute-search" placeholder="جستجو در ویژگی‌ها..." style="max-width: 250px;">
                        <div>
                            <span class="text-muted ml-2" id="selected-count">0 انتخاب شده</span>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="clear-all-attributes">پاک کردن همه</button>
                        </div>
                    </div>
                    <div class="attribute-tags-container" style="max-height: 250px; overflow-y: auto; scrollbar-width: thin;">
                        @php
                            $selectedAttributeIds = old('attributes', []);
                            if (empty($selectedAttributeIds) && isset($commodity) && $commodity->id && $commodity->relationLoaded('attributes')) {
                                $selectedAttributeIds = $commodity->attributes->pluck('id')->toArray();
                            }
                        @endphp
                        @foreach($attributes as $attribute)
                            <button type="button" 
                                    class="btn btn-sm attribute-tag mb-2 mr-1 {{ in_array($attribute->id, $selectedAttributeIds) ? 'btn-primary' : 'btn-outline-secondary' }}"
                                    data-attribute-id="{{ $attribute->id }}"
                                    data-name="{{ $attribute->name }}"
                                    title="{{ $attribute->description ?? '' }}">
                                {{ $attribute->name }}
                            </button>
                        @endforeach
                    </div>
                    {{-- Hidden inputs container for selected attributes --}}
                    <div id="selected-attributes-inputs">
                        @foreach($selectedAttributeIds as $attrId)
                            <input type="hidden" name="attributes[]" value="{{ $attrId }}">
                        @endforeach
                    </div>
                </div>
            </div>
            <small class="form-text text-muted">روی ویژگی‌ها کلیک کنید تا انتخاب شوند</small>
        @else
            <div class="alert alert-info">
                هیچ ویژگی‌ای تعریف نشده است. 
                @can('create_attribute', App\Models\Attribute::class)
                    <a href="{{ route('attribute.create') }}" target="_blank">یک ویژگی جدید ایجاد کنید</a>
                @endcan
            </div>
        @endif
    </div>
</div>