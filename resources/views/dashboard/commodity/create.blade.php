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
                                             <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                 {{ $unit->name }} ({{ $unit->symbol }})
                                             </option>
                                         @endforeach
                                    </select>
                                    <div class="invalid-feedback">واحد را انتخاب کنید</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="pieces_per_box">تعداد در کارتن</label>
                                    <input type="number" name="pieces_per_box" value="{{ old('pieces_per_box', 1) }}" class="form-control"
                                           id="pieces_per_box" min="1" placeholder="مثال: 24" required="">
                                    <div class="invalid-feedback">لطفاً تعداد در کارتن را وارد کنید</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="warning_limit"> {{ __('fields.warning_limit') }} <span class="unit_label">(کیلوگرم)</span></label>
                                    <input type="number" step="0.01" name="warning_limit"
                                           value="{{ old('warning_limit') }}"
                                           class="form-control" placeholder="{{ __('fields.warning_limit') }}" required>
                                    <div class="invalid-feedback">{{ __('fields.warning_limit') }} را وارد کنید</div>
                                </div>
                                <div id="profit_margin" class="form-group col-md-3">
                                    <label for="profit_margin">درصد سود (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" name="profit_margin"
                                           value="{{ old('profit_margin') }}"
                                           class="form-control" placeholder="درصد سود">
                                    <div class="invalid-feedback">درصد سود را وارد کنید</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div id="purchase_price" class="form-group col-md-6">
                                    <label for="purchase_price"> {{ __('fields.purchase_price') }} هر <span class="unit_label2">کیلوگرم</span> (ریال)</label>
                                    <input type="number" step="0.01" min="100" name="purchase_price"
                                           value="{{ old('purchase_price') }}" class="form-control"
                                           placeholder="{{ __('fields.purchase_price') }}" required>
                                   <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
                                </div>
                            </div>

                            <div id="product_formul" class="col-lg-12">
                                <p>فرمول ساخت محصول (مقادیر بر اساس واحد: <span id="product_unit_display" class="text-white font-weight-bold"></span>)</p>
                                                                 <div class="alert alert-info">
                                     <i class="ti-info-alt"></i>
                                     <strong>راهنما:</strong> فرمول ساخت برای هر واحد از محصول نهایی تعریف می‌شود.
                                 </div>
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
                                        <label
                                            for="material_amount">{{ __('fields.commodity.material_amount') }}</label>
                                        <input type="number" step="0.00001" name="material_amount[0]" class="form-control"
                                               id="material_amount"
                                               placeholder="{{ __('fields.commodity.material_amount') }}"
                                               min="0.00001">
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
        
        // Update required attributes based on type
        $('#type').on('change', function() {
            var selectedType = $(this).val();
            
            if (selectedType === 'product') {
                $('input[name="purchase_price"]').removeAttr('required');
                $('input[name="profit_margin"]').attr('required', 'required');
            } else if (selectedType === 'material') {
                $('input[name="purchase_price"]').attr('required', 'required');
                $('input[name="profit_margin"]').removeAttr('required');
            } else {
                $('input[name="purchase_price"]').removeAttr('required');
                $('input[name="profit_margin"]').removeAttr('required');
            }
        });
        
        // Handle unit selection change to update product unit display
        $('#unit').on('change', function() {
            updateProductUnitDisplay();
        });
        
        // Function to update product unit display
        function updateProductUnitDisplay() {
            var selectedUnit = $('#unit option:selected');
            var productUnitDisplay = $('#product_unit_display');
            
            if (selectedUnit.val() && $('#type').val() === 'product') {
                var unitName = selectedUnit.text();
                productUnitDisplay.text(unitName);
            } else {
                productUnitDisplay.text('');
            }
        }
        
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
            
            // Temporarily remove validation classes to allow submission
            $('form').removeClass('needs-validation was-validated');
            
            if (selectedType === 'product') {
                if (!selectedUnit) {
                    e.preventDefault();
                    alert('لطفاً واحد را برای محصول انتخاب کنید.');
                    $('#unit').focus();
                    return false;
                }
                
                // Validate product formula fields
                var hasMaterials = false;
                $('select[name^="materials"]').each(function() {
                    if ($(this).val()) {
                        hasMaterials = true;
                        return false; // break the loop
                    }
                });
                
                if (!hasMaterials) {
                    e.preventDefault();
                    alert('لطفاً حداقل یک ماده اولیه برای محصول انتخاب کنید.');
                    return false;
                }
            } else if (selectedType === 'material') {
                // Validate material fields
                var purchasePrice = $('input[name="purchase_price"]').val();
                if (!purchasePrice || purchasePrice <= 0) {
                    e.preventDefault();
                    alert('لطفاً قیمت خرید را برای ماده اولیه وارد کنید.');
                    $('input[name="purchase_price"]').focus();
                    return false;
                }
            }
            
            return true;
        });

        // add row
        $("#addRow").click(function () {
            var selectedType = $('#type').val();
            var requiredAttr = selectedType === 'product' ? 'required' : '';
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"><div class="form-group col-md-5"><label for="materials"> {{ __("fields.commodity.material_type") }}</label><select id="materials" class="form-control material-select" name="materials[1]" onchange="loadMaterialUnits(this)" ' + requiredAttr + '><option value="">انتخاب کنید...</option>@foreach ($materials as $material)<option value="{{ $material->id }}">{{ $material->title }}</option>@endforeach</select><div class="invalid-feedback">{{ __("fields.commodity.material_type") }} را انتخاب کنید</div></div><div class="form-group col-md-3"><label for="material_amount">{{ __("fields.commodity.material_amount") }}</label><input type="number" step="0.00001" name="material_amount[0]" class="form-control"id="material_amount"placeholder="{{ __("fields.commodity.material_amount") }}" min="0.00001" ' + requiredAttr + '><div class="invalid-feedback">لطفاً {{ __("fields.commodity.material_amount") }} را وارد کنید</div></div><div class="form-group col-md-2"><label for="material_unit">{{ __("fields.unit") }}</label><select name="material_units[0]" class="form-control material-unit-select" ' + requiredAttr + '><option value="">انتخاب کنید...</option></select><div class="invalid-feedback">واحد را انتخاب کنید</div></div><div class="form-group col-sm-auto"><label for="" class="d-none d-md-block">&nbsp;</label><button id="removeRowbtn" type="button" class="btn btn-danger btn-block py-2">حذف</button></div></div>';

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
            
            $('input[name="warning_limit"]').val('');
            $('input[name="purchase_price"]').val('');

            $('.unit_label').text(`(${unitSymbol})`);
            $('.unit_label2').text(unitSymbol);
        });

        $('input[name="warning_limit"]').on('change keyup paste', function(){
            $('input[name="warning_limit"]').val(Math.floor(this.value));
        });

        $('input[name="purchase_price"]').on('change keyup paste', function(){
            $('input[name="purchase_price"]').val(Math.floor(this.value));
        });



    </script>

    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/commodity.js') }}"></script>
@endsection
