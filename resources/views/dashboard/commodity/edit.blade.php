@extends('layouts.main')
@section('title', 'ویرایش کالا')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش کالا</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('commodity.update', $commodity) }}"
                              class="needs-validation"
                              novalidate="">
                            @method('PATCH')
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="title"> {{ __('fields.title') }}</label>
                                    <input type="text" name="title" value="{{ $commodity->title }}" class="form-control"
                                           id="title" placeholder="عنوان کالا" required="">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان کالا را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="type"> {{ __('fields.type') }}</label>
                                    <select id="type" class="form-control" name="type" disabled>
                                        @if ($commodity->type == 'material')
                                            <option value="material" selected>ماده اولیه</option>
                                            <option value="product">فرآورده</option>
                                        @else
                                            <option value="product" selected>فرآورده</option>
                                            <option value="material">ماده اولیه</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="type" value="{{ $commodity->type }}">
                                    <div class="invalid-feedback">نوع کالا را انتخاب کنید</div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="unit"> {{ __('fields.unit') }}</label>
                                    <select id="unit" class="form-control" name="unit_id" required>
                                        <option value="">انتخاب کنید...</option>
                                                                                 @foreach($units as $unit)
                                             <option value="{{ $unit->id }}" {{ $commodity->unit_id == $unit->id ? 'selected' : '' }}>
                                                 {{ $unit->name }} ({{ $unit->symbol }})
                                             </option>
                                         @endforeach
                                    </select>
                                    <div class="invalid-feedback">واحد را انتخاب کنید</div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="pieces_per_box">تعداد در کارتن</label>
                                    <input type="number" name="pieces_per_box" value="{{ $commodity->pieces_per_box ?? 1 }}" class="form-control"
                                           id="pieces_per_box" min="1" placeholder="مثال: 24" required="">
                                    <div class="invalid-feedback">لطفاً تعداد در کارتن را وارد کنید</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="warning_limit"> {{ __('fields.warning_limit') }} <span class="unit_label">({{ $commodity->unit ? $commodity->unit->symbol : '' }})</span></label>
                                    <input type="number" step="0.01" name="warning_limit"
                                           value="{{ $commodity->warning_limit }}"
                                           class="form-control" placeholder="{{ __('fields.warning_limit') }}" required>
                                    <div class="invalid-feedback">{{ __('fields.warning_limit') }} را وارد کنید</div>
                                </div>
                            @if($commodity->type == 'product')
                                    <div id="profit_margin" class="form-group col-md-6">
                                        <label for="profit_margin">درصد سود (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" name="profit_margin"
                                               value="{{ $commodity->profit_margin }}"
                                               class="form-control" placeholder="درصد سود"
                                               required>
                                        <div class="invalid-feedback">درصد سود را وارد کنید</div>
                                    </div>
                                @elseif(!empty($commodity->purchase_price))
                                    <div id="purchase_price" class="form-group col-md-6">
                                        <label for="purchase_price"> {{ __('fields.purchase_price') }} هر <span class="unit_label2">{{ $commodity->unit ? $commodity->unit->symbol : '' }}</span> (ریال)</label>
                                        <input type="number" step="0.01" min="100" name="purchase_price"
                                               value="{{ $commodity->purchase_price }}" class="form-control"
                                               placeholder="{{ __('fields.purchase_price') }}" required>
                                        <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
                                    </div>
                                @endif
                            </div>

                            @if ($commodity->type == 'product')
                                <div id="product_formul" class="col-lg-12">
                                    <p>فرمول ساخت محصول (مقادیر بر اساس واحد: <span id="product_unit_display" class="text-white font-weight-bold">{{ $commodity->unit ? $commodity->unit->name . ' (' . $commodity->unit->symbol . ')' : '' }}</span>)</p>
                                                                         <div class="alert alert-info">
                                         <i class="ti-info-alt"></i>
                                         <strong>راهنما:</strong> فرمول ساخت برای هر واحد از محصول نهایی تعریف می‌شود.
                                     </div>
                                    @foreach($used_materials as $used_material)
                                        <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                            <div class="form-group col-md-5"><label
                                                    for="materials"> {{ __("fields.commodity.material_type") }}</label>
                                                <select id="materials" class="form-control material-select" name="materials[]"
                                                        onchange="loadMaterialUnits(this)" required>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}"
                                                                @if($material->id == $used_material->id )
                                                                selected
                                                            @endif
                                                        >{{ $material->title }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">{{ __("fields.commodity.material_type") }}
                                                    را
                                                    انتخاب کنید
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label
                                                    for="material_amount">{{ __("fields.commodity.material_amount") }}</label>
                                                <input type="number" step="0.00001" name="material_amount[]"
                                                       class="form-control"
                                                       id="material_amount"
                                                       value="{{ $used_material->pivot->amount }}"
                                                       placeholder="{{ __("fields.commodity.material_amount") }}"
                                                       min="0.00001" required="">
                                                <div class="invalid-feedback">
                                                    لطفاً {{ __("fields.commodity.material_amount") }}
                                                    را وارد کنید
                                                </div>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="material_unit">{{ __('fields.unit') }}</label>
                                                <select name="material_units[]" class="form-control material-unit-select" required>
                                                    <option value="">انتخاب کنید...</option>
                                                </select>
                                                <div class="invalid-feedback">واحد را انتخاب کنید</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div id="newRow"></div>
                                    <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                                </div>
                            @endif
                            <button type="submit" class="btn btn-primary mr-2">ویرایش</button>
                            <a href="{{ route('commodity.index') }}" class="btn btn-danger">انصراف</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script type="text/javascript">
        // Preload material units data
        var materialUnitsData = {};
        @foreach($materials as $material)
            materialUnitsData[{{ $material->id }}] = [
                @foreach($material->selectable_units as $unit)
                    {
                        id: {{ $unit->id }},
                        name: '{{ $unit->name }}',
                        symbol: '{{ $unit->symbol }}',
                        display_name: '{{ $unit->name }} ({{ $unit->symbol }})'
                    }@if(!$loop->last),@endif
                @endforeach
            ];
        @endforeach

        // Function to load material units when material is selected
        function loadMaterialUnits(materialSelect) {
            var materialId = materialSelect.value;
            var unitSelect = materialSelect.closest('#inputFormRow').querySelector('.material-unit-select');

            // Clear unit options first
            unitSelect.innerHTML = '<option value="">انتخاب کنید...</option>';

            if (!materialId) {
                return;
            }

            // Get selectable units from preloaded data
            var units = materialUnitsData[materialId];
            if (units) {
                units.forEach(function(unit) {
                    var option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = unit.display_name;
                    unitSelect.appendChild(option);
                });
            }
        }

        // Initialize existing material rows on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.material-select').forEach(function(materialSelect) {
                if (materialSelect.value) {
                    loadMaterialUnits(materialSelect);

                    // Set the selected unit for existing materials
                    var unitSelect = materialSelect.closest('#inputFormRow').querySelector('.material-unit-select');
                    var selectedUnitId = '{{ $commodity->unit_id }}'; // Default to product unit

                    // Try to get the unit from the pivot data if available
                    @foreach($used_materials as $used_material)
                        if (materialSelect.value == {{ $used_material->id }}) {
                            selectedUnitId = '{{ $used_material->pivot->unit_id ?? $commodity->unit_id }}';
                        }
                    @endforeach

                    // Set the selected option after units are loaded
                    setTimeout(function() {
                        if (unitSelect && selectedUnitId) {
                            unitSelect.value = selectedUnitId;
                        }
                    }, 100);
                }
            });
        });

        // add row
        $("#addRow").click(function () {
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"><div class="form-group col-md-5"><label for="materials"> {{ __("fields.commodity.material_type") }}</label><select id="materials" class="form-control material-select" name="materials[1]" onchange="loadMaterialUnits(this)" required><option value="">انتخاب کنید...</option>@foreach ($materials as $material)<option value="{{ $material->id }}">{{ $material->title }}</option>@endforeach</select><div class="invalid-feedback">{{ __("fields.commodity.material_type") }} را انتخاب کنید</div></div><div class="form-group col-md-3"><label for="material_amount">{{ __("fields.commodity.material_amount") }}</label><input type="number" step="0.00001" name="material_amount[0]" class="form-control"id="material_amount"placeholder="{{ __("fields.commodity.material_amount") }}" min="0.00001" required=""><div class="invalid-feedback">لطفاً {{ __("fields.commodity.material_amount") }} را وارد کنید</div></div><div class="form-group col-md-2"><label for="material_unit">{{ __("fields.unit") }}</label><select name="material_units[0]" class="form-control material-unit-select" required><option value="">انتخاب کنید...</option></select><div class="invalid-feedback">واحد را انتخاب کنید</div></div><div class="form-group col-sm-auto"><label for="" class="d-none d-md-block">&nbsp;</label></div><i id="removeRow" type="submit" class="ti-close"></i></div>';

            $('#newRow').append(html);

            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('select[name^="materials"]').setAttribute('name', 'materials[' + index + ']');
                element.querySelector('input[name^="material_amount"]').setAttribute('name', 'material_amount[' + index + ']');
                element.querySelector('select[name^="material_units"]').setAttribute('name', 'material_units[' + index + ']');
            });
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).closest('#inputFormRow').remove();
            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('select[name^="materials"]').setAttribute('name', 'materials[' + index + ']');
                element.querySelector('input[name^="material_amount"]').setAttribute('name', 'material_amount[' + index + ']');
                element.querySelector('select[name^="material_units"]').setAttribute('name', 'material_units[' + index + ']');
            });
        });

                 var warning_limit = 0;
         var purchase_price = 0;

        setTimeout(() => {

            warning_limit = $('input[name="warning_limit"]').val();
            purchase_price = $('input[name="purchase_price"]').val();

        }, 1000);

        function updatevals(){
            warning_limit = $('input[name="warning_limit"]').val();
            purchase_price = $('input[name="purchase_price"]').val();
        }

                 $('#unit').on('change', function() {
             const selectedUnit = $(this).find('option:selected');
             const unitSymbol = selectedUnit.text().match(/\((.*?)\)/)[1];
             $('.unit_label').text(`(${unitSymbol})`);
             $('.unit_label2').text(unitSymbol);

             // Update labels for all units dynamically
             $('.unit_label').text(`(${unitSymbol})`);
             $('.unit_label2').text(unitSymbol);

             // Update product unit display in formula section
             updateProductUnitDisplay();

             // For now, we'll use a simplified approach
             // In the future, this could be enhanced to use database conversion rates
             updateUnitLabels(unitSymbol);
         });

        function updateUnitLabels(unitSymbol) {
            // Update all unit labels to show the selected unit
            $('.unit_label').text(`(${unitSymbol})`);
            $('.unit_label2').text(unitSymbol);
        }

        // Function to update product unit display in formula section
        function updateProductUnitDisplay() {
            var selectedUnit = $('#unit option:selected');
            var productUnitDisplay = $('#product_unit_display');

            if (selectedUnit.val()) {
                var unitName = selectedUnit.text();
                productUnitDisplay.text(unitName);
            } else {
                productUnitDisplay.text('');
            }
        }

        // Simplified conversion function - in the future this could use database conversion rates
        function convertValue(value, fromUnit, toUnit) {
            // For now, we'll use a simple approach
            // In production, this should use the database conversion rates
            if (fromUnit === toUnit) {
                return value;
            }

            // This is a placeholder - the actual conversion should come from the database
            // For now, we'll just return the original value and let the user adjust manually
            return value;
        }


        $('input[name="warning_limit"]').on('change keyup paste', function(){
            // For now, store the value as-is since we're not doing automatic conversions
            // In the future, this could use database conversion rates
            $('input[name="warning_limit"]').val(Math.floor(this.value));
            updatevals();
        });

        $('input[name="purchase_price"]').on('change keyup paste', function(){
            // For now, store the value as-is since we're not doing automatic conversions
            // In the future, this could use database conversion rates
            $('input[name="purchase_price"]').val(Math.floor(this.value));
            updatevals();
        });

        // Trigger type change on page load if type is already selected
        $(document).ready(function() {
            if ($('#type').val()) {
                $('#type').trigger('change');
            }
        });

        // Form validation before submission
        $('form').on('submit', function(e) {
            var selectedType = $('input[name="type"]').val() || $('#type').val();
            var selectedUnit = $('#unit').val();

            if (selectedType === 'product') {
                if (!selectedUnit) {
                    e.preventDefault();
                    alert('لطفاً واحد را برای محصول انتخاب کنید.');
                    $('#unit').focus();
                    return false;
                }
            }
        });
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/commodity.js') }}"></script>
@endsection
