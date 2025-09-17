{{-- Form fields partial for production request create/edit --}}
<div class="form-row m-3">
    <div class="form-group col-md-6">
        <label for="product_id">{{ __('fields.production-request.product_id') }}</label>
        <select id="product_id" class="form-control" name="product_id" required>
            <option value="">انتخاب کنید</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" 
                        {{ (isset($request) && $request->product_id == $product->id) ? 'selected' : '' }}
                        data-materials="{{ $product->materials->count() }}"
                        data-unit="{{ $product->unit ? $product->unit->symbol : '' }}">
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
