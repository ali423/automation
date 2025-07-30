@extends('layouts.main')
@section('title', 'ایجاد کالا جدید')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">کالا جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('commodity.store') }}" class="needs-validation"
                              novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="title"> {{ __('fields.title') }}</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                           id="title" placeholder="عنوان کالا" required="">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان کالا را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="type"> {{ __('fields.type') }}</label>
                                    <select id="type" class="form-control" name="type" required>
                                        <option value="">انتخاب کنید...</option>
                                        <option value="material">ماده اولیه</option>
                                        <option value="product">فرآورده</option>
                                    </select>
                                    <div class="invalid-feedback">نوع کالا را انتخاب کنید</div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="unit"> {{ __('fields.unit') }}</label>
                                    <select id="unit" class="form-control" name="unit_id" required>
                                        <option value="">انتخاب کنید...</option>
                                        @foreach($units as $unit)
                                            @if($unit->symbol === 'kg' || old('type') !== 'product')
                                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->name }} ({{ $unit->symbol }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">واحد را انتخاب کنید</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="fake_warning_limit"> {{ __('fields.warning_limit') }} <span class="unit_label">(کیلوگرم)</span></label>
                                    <input type="number" step="0.01" name="fake_warning_limit"
                                           value="{{ old('warning_limit') }}"
                                           class="form-control" placeholder="{{ __('fields.warning_limit') }}" required>
                                           <input type="number" name="warning_limit" class="d-none">
                                    <div class="invalid-feedback">{{ __('fields.warning_limit') }} را وارد کنید</div>
                                </div>
                                <div id="sales_price" class="form-group col-md-6">
                                    <label for="sales_price"> {{ __('fields.sales_price') }} هر <span class="unit_label2">کیلوگرم</span> (ریال)</label>
                                    <input type="number" step="0.01" min="100" name="fake_sales_price"
                                           value="{{ old('sales_price') }}"
                                           class="form-control" placeholder="{{ __('fields.sales_price') }}" required>
                                           <input type="number" name="sales_price" class="d-none">
                                    <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
                                </div>
                                <div id="purchase_price" class="form-group col-md-6">
                                    <label for="purchase_price"> {{ __('fields.purchase_price') }} هر <span class="unit_label2">کیلوگرم</span> (ریال)</label>
                                    <input type="number" step="0.01" min="100" name="fake_purchase_price"
                                           value="{{ old('purchase_price') }}" class="form-control"
                                           placeholder="{{ __('fields.purchase_price') }}" required>
                                    <input type="number" name="purchase_price" class="d-none">
                                   <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
                                </div>
                            </div>

                            <div id="product_formul" class="col-lg-12">
                                <p>فرمول ساخت محصول (مقادیر بر اساس واحد)</p>
                                <div class="alert alert-info">
                                    <i class="ti-info-alt"></i>
                                    <strong>راهنما:</strong> فرمول ساخت برای هر ۱۸۵ کیلوگرم محصول نهایی تعریف می‌شود.
                                </div>
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    <div class="form-group col-md-5">
                                        <label for="materials"> {{ __('fields.commodity.material_type') }}</label>
                                        <select id="materials" class="form-control material-select" name="materials[0]" onchange="loadMaterialUnits(this)" required>
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
                                        <label
                                            for="material_amount">{{ __('fields.commodity.material_amount') }}</label>
                                        <input type="number" step="0.01" name="material_amount[0]" class="form-control"
                                               id="material_amount"
                                               placeholder="{{ __('fields.commodity.material_amount') }}"
                                               min="0.01" required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{ __('fields.commodity.material_amount') }} را وارد کنید
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="material_unit">{{ __('fields.unit') }}</label>
                                        <select name="material_units[0]" class="form-control material-unit-select" required>
                                            <option value="">انتخاب کنید...</option>
                                        </select>
                                        <div class="invalid-feedback">واحد را انتخاب کنید</div>
                                    </div>
                                </div>

                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">ثبت کالا</button>
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
        
        // Handle commodity type change
        $('#type').on('change', function() {
            var selectedType = $(this).val();
            var unitSelect = $('#unit');
            var productFormula = $('#product_formul');
            
            // Clear current selection
            unitSelect.val('');
            
            if (selectedType === 'product') {
                // For products, only show kg unit
                unitSelect.find('option').hide();
                unitSelect.find('option:first').show(); // Show "انتخاب کنید..."
                unitSelect.find('option').each(function() {
                    var optionText = $(this).text();
                    if (optionText.includes('kg') || optionText.includes('کیلوگرم')) {
                        $(this).show();
                    }
                });
                
                // Show product formula section
                productFormula.show();
            } else if (selectedType === 'material') {
                // For materials, show all units
                unitSelect.find('option').show();
                
                // Hide product formula section
                productFormula.hide();
            } else {
                // No type selected, show all units and hide formula
                unitSelect.find('option').show();
                productFormula.hide();
            }
        });
        
        // Trigger type change on page load if type is already selected
        $(document).ready(function() {
            if ($('#type').val()) {
                $('#type').trigger('change');
            }
        });

        // Form validation before submission
        $('form').on('submit', function(e) {
            var selectedType = $('#type').val();
            var selectedUnit = $('#unit').val();
            
            if (selectedType === 'product') {
                if (!selectedUnit) {
                    e.preventDefault();
                    alert('لطفاً واحد کیلوگرم را برای محصول انتخاب کنید.');
                    $('#unit').focus();
                    return false;
                }
                
                // Check if the selected unit is kg
                var unitText = $('#unit option:selected').text();
                if (!unitText.includes('kg') && !unitText.includes('کیلوگرم')) {
                    e.preventDefault();
                    alert('محصولات باید از واحد کیلوگرم استفاده کنند.');
                    $('#unit').focus();
                    return false;
                }
            }
        });

        // add row
        $("#addRow").click(function () {
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"><div class="form-group col-md-5"><label for="materials"> {{ __("fields.commodity.material_type") }}</label><select id="materials" class="form-control material-select" name="materials[1]" onchange="loadMaterialUnits(this)" required><option value="">انتخاب کنید...</option>@foreach ($materials as $material)<option value="{{ $material->id }}">{{ $material->title }}</option>@endforeach</select><div class="invalid-feedback">{{ __("fields.commodity.material_type") }} را انتخاب کنید</div></div><div class="form-group col-md-3"><label for="material_amount">{{ __("fields.commodity.material_amount") }}</label><input type="number" step="0.01" name="material_amount[0]" class="form-control"id="material_amount"placeholder="{{ __("fields.commodity.material_amount") }}" min="0.01" required=""><div class="invalid-feedback">لطفاً {{ __("fields.commodity.material_amount") }} را وارد کنید</div></div><div class="form-group col-md-2"><label for="material_unit">{{ __("fields.unit") }}</label><select name="material_units[0]" class="form-control material-unit-select" required><option value="">انتخاب کنید...</option></select><div class="invalid-feedback">واحد را انتخاب کنید</div></div><div class="form-group col-sm-auto"><label for="" class="d-none d-md-block">&nbsp;</label><button id="removeRowbtn" type="submit" class="btn btn-danger btn-block py-2">حذف</button></div></div>';

            $('#newRow').append(html);

            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('select[name^="materials"]').setAttribute('name', 'materials[' + index + ']');
                element.querySelector('input[name^="material_amount"]').setAttribute('name', 'material_amount[' + index + ']');
                element.querySelector('select[name^="material_units"]').setAttribute('name', 'material_units[' + index + ']');
            });
        });

        // remove row
        $(document).on('click', '#removeRowbtn', function () {
            $(this).closest('#inputFormRow').remove();
            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('select[name^="materials"]').setAttribute('name', 'materials[' + index + ']');
                element.querySelector('input[name^="material_amount"]').setAttribute('name', 'material_amount[' + index + ']');
                element.querySelector('select[name^="material_units"]').setAttribute('name', 'material_units[' + index + ']');
            });
        });

        $('#unit').on('change', function() {
            const selectedUnit = $(this).find('option:selected');
            const unitSymbol = selectedUnit.text().match(/\((.*?)\)/)[1];
            
            $('input[name="fake_warning_limit"]').val('');
            $('input[name="fake_sales_price"]').val('');
            $('input[name="fake_purchase_price"]').val('');
            $('input[name="warning_limit"]').val('');
            $('input[name="sales_price"]').val('');
            $('input[name="purchase_price"]').val('');

            $('.unit_label').text(`(${unitSymbol})`);
            $('.unit_label2').text(unitSymbol);
        });

        $('input[name="fake_warning_limit"]').on('change keyup paste', function(){
            $('input[name="warning_limit"]').val(Math.floor(this.value));
        });

        $('input[name="fake_sales_price"]').on('change keyup paste', function(){
            $('input[name="sales_price"]').val(Math.floor(this.value));
        });

        $('input[name="fake_purchase_price"]').on('change keyup paste', function(){
            $('input[name="purchase_price"]').val(Math.floor(this.value));
        });

    </script>

    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/commodity.js') }}"></script>
@endsection
