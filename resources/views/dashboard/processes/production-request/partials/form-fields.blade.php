{{-- Form fields partial for production request create/edit --}}

{{-- Attribute Filters --}}
@if(isset($attributes) && $attributes->count() > 0)
<div class="form-row m-3">
    <div class="col-12">
        <div class="card" style="background-color: #f8f9fa;">
            <div class="card-body p-3">
                <h6 class="mb-2">فیلتر بر اساس ویژگی‌ها:</h6>
                <div class="row" id="production-attribute-filters">
                    @foreach($attributes as $attribute)
                        <div class="col-md-3 col-sm-4 col-xs-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input production-attribute-filter" 
                                       type="checkbox" 
                                       value="{{ $attribute->id }}" 
                                       id="prod_attr_{{ $attribute->id }}">
                                <label class="form-check-label" for="prod_attr_{{ $attribute->id }}">
                                    {{ $attribute->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-12 mt-2">
                        <button type="button" class="btn btn-sm btn-secondary" id="clear-production-filters">
                            پاک کردن فیلترها
                        </button>
                        <small class="text-muted ml-3">
                            <i class="fas fa-info-circle"></i> 
                            فقط محصولاتی با ویژگی‌های انتخاب شده نمایش داده می‌شوند
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="form-row m-3">
    <div class="form-group col-md-6">
        <label for="product_id">{{ __('fields.production-request.product_id') }}</label>
        <select id="product_id" class="form-control" name="product_id" required>
            <option value="">انتخاب کنید</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" 
                        {{ (isset($request) && $request->product_id == $product->id) ? 'selected' : '' }}
                        data-materials="{{ $product->materials->count() }}"
                        data-unit="{{ $product->unit ? $product->unit->symbol : '' }}"
                        data-attributes="{{ $product->attributes->pluck('id')->join(',') }}">
                    {{ $product->title }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback">
            محصول را انتخاب کنید
        </div>
    </div>
    <div class="form-group col-md-6">
        <label for="amount">{{ __('fields.production-request.production_amount') }} <span id="unit-display"></span></label>
        <input type="number" class="form-control" id="amount" name="amount" min="0.001" step="0.001" 
               value="{{ old('amount', isset($request) ? $request->production_amount : '') }}" required>
        <div class="invalid-feedback">
            مقدار تولید را وارد کنید
        </div>
    </div>
</div>
