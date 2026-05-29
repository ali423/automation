{{-- Form fields partial for production request create/edit --}}

{{-- Attribute Filters --}}
@if(isset($attributes) && $attributes->count() > 0)
<div class="form-row m-3">
    <div class="col-12 mb-2 attribute-filter-container">
        <div class="d-inline-flex align-items-center" style="cursor: pointer;" onclick="$(this).closest('.attribute-filter-container').find('.filter-panel').slideToggle(200);">
            <i class="ti-filter toggle-row-filter" style="font-size: 12px; color: #666;"></i>
            <small style="margin-right: 6px; font-size: 11px; color: #333;">فیلتر ویژگی</small>
        </div>
        <div class="filter-panel mt-2" style="display: none;">
            <div class="card" style="background-color: #f8f9fa; border: 1px solid #e0e0e0;">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <input type="text" class="form-control form-control-sm" id="production-attribute-search" placeholder="جستجو..." style="font-size: 12px; width: 150px;">
                        <button type="button" class="btn btn-xs btn-secondary" id="clear-production-filters" style="font-size: 11px; padding: 2px 8px;">پاک کردن</button>
                    </div>
                    <div class="d-flex flex-wrap gap-1" id="production-attribute-filters" style="max-height: 150px; overflow-y: auto; scrollbar-width: thin;">
                        @foreach($attributes as $attribute)
                            <button type="button" class="btn btn-xs btn-outline-primary production-attribute-filter" data-attribute-id="{{ $attribute->id }}" data-name="{{ $attribute->name }}" style="font-size: 11px; padding: 2px 8px; margin: 2px;">
                                {{ $attribute->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="form-row m-3">
    <div class="form-group col-md-4">
        <label for="product_id">{{ __('fields.production-request.product_id') }}</label>
        <select id="product_id" class="form-control" name="product_id" required>
            <option value="">انتخاب کنید</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" 
                        {{ (isset($request) && $request->product_id == $product->id) ? 'selected' : '' }}
                        data-materials="{{ $product->materials->count() }}"
                        data-unit="{{ $product->unit ? $product->unit->symbol : '' }}"
                        data-pieces-per-box="{{ $product->pieces_per_box ?? '' }}"
                        data-attributes="{{ $product->attributes->pluck('id')->join(',') }}">
                    {{ $product->title }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback">
            محصول را انتخاب کنید
        </div>
    </div>
    <div class="form-group col-md-4">
        <label for="amount">{{ __('fields.production-request.production_amount') }} <span id="unit-display"></span></label>
        <input type="number" class="form-control" id="amount" name="amount" min="0.001" step="0.001" 
               value="{{ old('amount', isset($request) ? $request->production_amount : '') }}" required>
        <div class="invalid-feedback">
            مقدار تولید را وارد کنید
        </div>
    </div>
    <div class="form-group col-md-2" id="packaging_count_group" style="display: none;">
        <label for="packaging_count">تعداد بسته</label>
        <input type="number" class="form-control" id="packaging_count" name="packaging_count" min="1" step="1"
               value="{{ old('packaging_count', isset($request) ? $request->packaging_count : '') }}"
               autocomplete="off" placeholder="">
    </div>
    <div class="form-group col-md-2" id="pieces_per_box_group" style="display: none;">
        <label for="pieces_per_box_display">تعداد در کارتن</label>
        <input type="text" class="form-control" id="pieces_per_box_display" readonly placeholder="-">
    </div>
</div>
