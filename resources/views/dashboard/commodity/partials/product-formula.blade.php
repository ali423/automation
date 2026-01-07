{{-- Product formula partial for material selection --}}
@if(!$commodity->id || $commodity->type == 'product')
<div id="product_formul" class="col-lg-12">
    <p>فرمول ساخت محصول (مقادیر بر اساس واحد: <span id="product_unit_display" class="text-white font-weight-bold">{{ $commodity->unit?->name && $commodity->unit?->symbol ? $commodity->unit->name . ' (' . $commodity->unit->symbol . ')' : '' }}</span>)</p>
    <div class="alert alert-info">
        <i class="ti-info-alt"></i>
        <strong>راهنما:</strong> فرمول ساخت برای هر واحد از محصول نهایی تعریف می‌شود.
    </div>
    
    @if(isset($used_materials) && count($used_materials) > 0)
        {{-- Show existing materials for edit form --}}
        @foreach($used_materials as $used_material)
            <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                <div class="form-group col-md-5">
                    <label for="materials"> {{ __('fields.commodity.material_type') }}</label>
                    <select id="materials" class="form-control material-select" name="materials[]" onchange="loadMaterialUnits(this)" required>
                        <option value="">انتخاب کنید...</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}" {{ $material->id == $used_material->id ? 'selected' : '' }}>
                                {{ $material->title }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">
                        {{ __('fields.commodity.material_type') }} را انتخاب کنید
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label for="material_amount">{{ __('fields.commodity.material_amount') }}</label>
                    <input type="number" step="1" name="material_amount[]" class="form-control"
                           id="material_amount" value="{{ $used_material->pivot->amount ? number_format($used_material->pivot->amount, 0, '.', '') : '' }}"
                           placeholder="{{ __('fields.commodity.material_amount') }}" min="1" required>
                    <div class="invalid-feedback">
                        لطفاً {{ __('fields.commodity.material_amount') }} را وارد کنید
                    </div>
                </div>
                <div class="form-group col-md-2">
                    <label for="material_unit">{{ __('fields.unit') }}</label>
                    <select name="material_units[]" class="form-control material-unit-select" required>
                        <option value="">انتخاب کنید...</option>
                    </select>
                    <div class="invalid-feedback">واحد را انتخاب کنید</div>
                </div>
                <div class="form-group col-sm-auto">
                    <label for="" class="d-none d-md-block">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-block py-2 remove-row-btn">حذف</button>
                </div>
            </div>
        @endforeach
    @else
        {{-- Show empty form for create --}}
        <div id="inputFormRow" class="form-row shadow p-4 mb-3">
            <div class="form-group col-md-5">
                <label for="materials"> {{ __('fields.commodity.material_type') }}</label>
                <select id="materials" class="form-control material-select" name="materials[0]" onchange="loadMaterialUnits(this)">
                    <option value="">انتخاب کنید...</option>
                    @foreach ($materials as $material)
                        <option value="{{ $material->id }}">{{ $material->title }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    {{ __('fields.commodity.material_type') }} را انتخاب کنید
                </div>
            </div>
            <div class="form-group col-md-3">
                <label for="material_amount">{{ __('fields.commodity.material_amount') }}</label>
                <input type="number" step="1" name="material_amount[0]" class="form-control"
                       id="material_amount" placeholder="{{ __('fields.commodity.material_amount') }}" min="1">
                <div class="invalid-feedback">
                    لطفاً {{ __('fields.commodity.material_amount') }} را وارد کنید
                </div>
            </div>
            <div class="form-group col-md-2">
                <label for="material_unit">{{ __('fields.unit') }}</label>
                <select name="material_units[0]" class="form-control material-unit-select">
                    <option value="">انتخاب کنید...</option>
                </select>
                <div class="invalid-feedback">واحد را انتخاب کنید</div>
            </div>
        </div>
    @endif

    <div id="newRow"></div>
    <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
</div>
@endif
