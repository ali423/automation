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
                                    <div class="invalid-feedback">نوع کالا را انتخاب کنید</div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="unit"> {{ __('fields.unit') }}</label>
                                    <select id="unit" class="form-control" name="unit_id" required>
                                        <option value="">انتخاب کنید...</option>
                                        @foreach($units as $unit)
                                            @if($unit->symbol === 'kg' || $commodity->type !== 'product')
                                                <option value="{{ $unit->id }}" {{ $commodity->unit_id == $unit->id ? 'selected' : '' }}>
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
                                    <label for="fake_warning_limit"> {{ __('fields.warning_limit') }} <span class="unit_label">({{ $commodity->unit ? $commodity->unit->symbol : '' }})</span></label>
                                    <input type="number" step="0.01" name="fake_warning_limit"
                                           value="{{ $commodity->warning_limit }}"
                                           class="form-control" placeholder="{{ __('fields.warning_limit') }}" required>
                                    <input type="number" name="warning_limit"
                                           value="{{ $commodity->warning_limit }}"
                                           class="form-control d-none" placeholder="{{ __('fields.warning_limit') }}">
                                    <div class="invalid-feedback">{{ __('fields.warning_limit') }} را وارد کنید</div>
                                </div>
                            @if(!empty($commodity->sales_price))
                                    <div id="sales_price" class="form-group col-md-6">
                                        <label for="fake_sales_price"> {{ __('fields.sales_price') }} هر <span class="unit_label2">{{ $commodity->unit ? $commodity->unit->symbol : '' }}</span> (ریال)</label>
                                        <input type="number" step="0.01" min="100" name="fake_sales_price"
                                               value="{{ $commodity->sales_price }}"
                                               class="form-control" placeholder="{{ __('fields.sales_price') }}"
                                               required>
                                        <input type="number" name="sales_price"
                                               value="{{ $commodity->sales_price }}"
                                               class="form-control d-none" placeholder="{{ __('fields.sales_price') }}">
                                        <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
                                    </div>
                                @elseif(!empty($commodity->purchase_price))
                                    <div id="purchase_price" class="form-group col-md-6">
                                        <label for="fake_purchase_price"> {{ __('fields.purchase_price') }} هر <span class="unit_label2">{{ $commodity->unit ? $commodity->unit->symbol : '' }}</span> (ریال)</label>
                                        <input type="number" step="0.01" min="100" name="fake_purchase_price"
                                               value="{{ $commodity->purchase_price }}" class="form-control"
                                               placeholder="{{ __('fields.purchase_price') }}" required>
                                        <input type="number" name="purchase_price"
                                               value="{{ $commodity->purchase_price }}" class="form-control d-none"
                                               placeholder="{{ __('fields.purchase_price') }}">
                                        <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
                                    </div>
                                @endif
                            </div>

                            @if ($commodity->type == 'product')
                                <div id="product_formul" class="col-lg-12">
                                    <p>فرمول ساخت محصول (مقادیر بر اساس واحد)</p>
                                    <div class="alert alert-info">
                                        <i class="ti-info-alt"></i>
                                        <strong>راهنما:</strong> فرمول ساخت برای هر ۱۸۵ کیلوگرم محصول نهایی تعریف می‌شود.
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
                                                <input type="number" step="0.01" name="material_amount[]"
                                                       class="form-control"
                                                       id="material_amount"
                                                       value="{{ $used_material->pivot->amount ?? $used_material->pivot->percentage }}"
                                                       placeholder="{{ __("fields.commodity.material_amount") }}"
                                                       min="0.01" required="">
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
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"><div class="form-group col-md-5"><label for="materials"> {{ __("fields.commodity.material_type") }}</label><select id="materials" class="form-control material-select" name="materials[1]" onchange="loadMaterialUnits(this)" required><option value="">انتخاب کنید...</option>@foreach ($materials as $material)<option value="{{ $material->id }}">{{ $material->title }}</option>@endforeach</select><div class="invalid-feedback">{{ __("fields.commodity.material_type") }} را انتخاب کنید</div></div><div class="form-group col-md-3"><label for="material_amount">{{ __("fields.commodity.material_amount") }}</label><input type="number" step="0.01" name="material_amount[0]" class="form-control"id="material_amount"placeholder="{{ __("fields.commodity.material_amount") }}" min="0.01" required=""><div class="invalid-feedback">لطفاً {{ __("fields.commodity.material_amount") }} را وارد کنید</div></div><div class="form-group col-md-2"><label for="material_unit">{{ __("fields.unit") }}</label><select name="material_units[0]" class="form-control material-unit-select" required><option value="">انتخاب کنید...</option></select><div class="invalid-feedback">واحد را انتخاب کنید</div></div><div class="form-group col-sm-auto"><label for="" class="d-none d-md-block">&nbsp;</label></div><i id="removeRow" type="submit" class="ti-close"></i></div>';

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

        var preunit = "kg";
        var warning_limit = 0;
        var purchase_price = 0;
        var sales_price = 0;

        setTimeout(() => {

            warning_limit = $('input[name="warning_limit"]').val();
            purchase_price = $('input[name="purchase_price"]').val();
            sales_price = $('input[name="sales_price"]').val();

        }, 1000);

        function updatevals(){
            warning_limit = $('input[name="warning_limit"]').val();
            purchase_price = $('input[name="purchase_price"]').val();
            sales_price = $('input[name="sales_price"]').val();
        }

        $('#unit').on('change', function() {
            const selectedUnit = $(this).find('option:selected');
            const unitSymbol = selectedUnit.text().match(/\((.*?)\)/)[1];
            $('.unit_label').text(`(${unitSymbol})`);
            $('.unit_label2').text(unitSymbol);
            
            switch (this.value) {
                case 'kg':
                    $('.unit_label').text('(کیلوگرم)'); 
                    modifyinputs(preunit,'kg');
                    break;
                case 'barrel':
                    $('.unit_label').text('(بشکه)'); 
                    modifyinputs(preunit,'barrel');
                    break;
                case 'galon':
                    $('.unit_label').text('(گالن 20 لیتری)'); 
                    modifyinputs(preunit,'galon');
                    break;

                default:
                    break;
            }
        });

        function modifyinputs(oldunit,newunit){

            switch (newunit) {
                case 'kg':
                $('input[name="fake_warning_limit"]').val(Math.floor(warning_limit));
                $('input[name="fake_purchase_price"]').val(Math.floor(purchase_price));
                $('input[name="fake_sales_price"]').val(Math.floor(sales_price));
                    break;
                case 'barrel':
                $('input[name="fake_warning_limit"]').val(Math.floor(warning_limit/185));
                $('input[name="fake_purchase_price"]').val(Math.floor(purchase_price*185));
                $('input[name="fake_sales_price"]').val(Math.floor(sales_price*185));
                    break;
                case 'galon':
                $('input[name="fake_warning_limit"]').val(Math.floor(warning_limit/17.8));
                $('input[name="fake_purchase_price"]').val(Math.floor(purchase_price*17.8));
                $('input[name="fake_sales_price"]').val(Math.floor(sales_price*17.8));
                    break;
            
                default:
                    break;
            }
            
            preunit = newunit;
        }


        $('input[name="fake_warning_limit"]').on('change keyup paste', function(){

            switch ($('#unit option:selected').val()) {
                case 'kg':
                    $('input[name="warning_limit"]').val(Math.floor(this.value));
                    break;
                case 'barrel':
                    $('input[name="warning_limit"]').val(Math.floor(this.value*185));
                    break;
                case 'galon':
                    $('input[name="warning_limit"]').val(Math.floor(this.value*17.8));
                    break;

                default:
                    break;
            }
            updatevals();
        })
        $('input[name="fake_sales_price"]').on('change keyup paste', function(){

            switch ($('#unit option:selected').val()) {
                case 'kg':
                    $('input[name="sales_price"]').val(Math.floor(this.value));
                    break;
                case 'barrel':
                    $('input[name="sales_price"]').val(Math.floor(this.value/185));
                    break;
                case 'galon':
                    $('input[name="sales_price"]').val(Math.floor(this.value/17.8));
                    break;

                default:
                    break;
            }
            updatevals();
        })
        $('input[name="fake_purchase_price"]').on('change keyup paste', function(){

            switch ($('#unit option:selected').val()) {
                case 'kg':
                    $('input[name="purchase_price"]').val(Math.floor(this.value));
                    break;
                case 'barrel':
                    $('input[name="purchase_price"]').val(Math.floor(this.value/185));
                    break;
                case 'galon':
                    $('input[name="purchase_price"]').val(Math.floor(this.value/17.8));
                    break;

                default:
                    break;
            }
            updatevals();
        })

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
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/commodity.js') }}"></script>
@endsection
