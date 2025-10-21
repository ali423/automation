{{-- Shared JavaScript for commodity forms --}}
<script type="text/javascript">
    // Preload material units data
    var materialUnitsData = {};
    @if(isset($materials))
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
    @endif

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
            $('input[name="sales_price"]').attr('required', 'required');
            $('input[name="product_identifier"]').attr('required', 'required');
            $('input[name="weight_per_unit"]').attr('required', 'required');
            $('#pieces_per_box_group').show();
            $('#product_identifier_group').show();
            $('#weight_per_unit_group').show();
            $('#sales_price').show();
            $('#purchase_price').hide();
        } else if (selectedType === 'material') {
            $('input[name="purchase_price"]').attr('required', 'required');
            $('input[name="sales_price"]').removeAttr('required');
            $('input[name="product_identifier"]').removeAttr('required');
            $('input[name="weight_per_unit"]').removeAttr('required');
            $('#pieces_per_box_group').hide();
            $('#product_identifier_group').hide();
            $('#weight_per_unit_group').hide();
            $('#sales_price').hide();
            $('#purchase_price').show();
        } else {
            $('input[name="purchase_price"]').removeAttr('required');
            $('input[name="sales_price"]').removeAttr('required');
            $('input[name="product_identifier"]').removeAttr('required');
            $('input[name="weight_per_unit"]').removeAttr('required');
            $('#pieces_per_box_group').hide();
            $('#product_identifier_group').hide();
            $('#weight_per_unit_group').hide();
            $('#sales_price').hide();
            $('#purchase_price').hide();
        }
    });
    
    // Handle unit selection change to update product unit display
    $('#unit').on('change', function() {
        updateProductUnitDisplay();
        updateUnitLabels();
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
    
    // Function to update unit labels
    function updateUnitLabels() {
        const selectedUnit = $('#unit option:selected');
        const unitSymbol = selectedUnit.text().match(/\((.*?)\)/);
        
        if (unitSymbol && selectedUnit.val()) {
            $('.unit_label').text(`(${unitSymbol[1]})`);
            $('.unit_label2').text(unitSymbol[1]);
        } else {
            // Show default placeholder when no unit is selected
            $('.unit_label').text('(واحد)');
            $('.unit_label2').text('واحد');
        }
    }
    
    // Initialize existing material rows on page load (for edit form)
    document.addEventListener('DOMContentLoaded', function() {
        // Hide pieces_per_box and product_identifier fields by default
        $('#pieces_per_box_group').hide();
        $('#product_identifier_group').hide();
        
        if ($('#type').val()) {
            $('#type').trigger('change');
        }
        
        // Initialize unit labels on page load
        updateUnitLabels();
        
        // Initialize material units for existing materials
        document.querySelectorAll('.material-select').forEach(function(materialSelect) {
            if (materialSelect.value) {
                loadMaterialUnits(materialSelect);
                
                // Set the selected unit for existing materials
                var unitSelect = materialSelect.closest('#inputFormRow').querySelector('.material-unit-select');
                var selectedUnitId = '{{ isset($commodity) ? $commodity->unit_id : '' }}';
                
                // Try to get the unit from the pivot data if available
                @if(isset($used_materials))
                    @foreach($used_materials as $used_material)
                        if (materialSelect.value == {{ $used_material->id }}) {
                            selectedUnitId = '{{ $used_material->pivot->unit_id ?? (isset($commodity) ? $commodity->unit_id : '') }}';
                        }
                    @endforeach
                @endif
                
                // Set the selected option after units are loaded
                setTimeout(function() {
                    if (unitSelect && selectedUnitId) {
                        unitSelect.value = selectedUnitId;
                    }
                }, 100);
            }
        });
    });

    // Form validation before submission
    $('form').on('submit', function(e) {
        var selectedType = $('input[name="type"]').val() || $('#type').val();
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
            
            // Validate product identifier
            var productIdentifier = $('input[name="product_identifier"]').val();
            if (!productIdentifier || productIdentifier.trim() === '') {
                e.preventDefault();
                alert('لطفاً شناسه کالا را برای محصول وارد کنید.');
                $('input[name="product_identifier"]').focus();
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

    // Add row functionality
    $("#addRow").click(function () {
        var selectedType = $('#type').val();
        var requiredAttr = selectedType === 'product' ? 'required' : '';
        var materialsOptions = '';
        @if(isset($materials))
            @foreach($materials as $material)
                materialsOptions += '<option value="{{ $material->id }}">{{ $material->title }}</option>';
            @endforeach
        @endif
        var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"><div class="form-group col-md-5"><label for="materials"> {{ __("fields.commodity.material_type") }}</label><select id="materials" class="form-control material-select" name="materials[1]" onchange="loadMaterialUnits(this)" ' + requiredAttr + '><option value="">انتخاب کنید...</option>' + materialsOptions + '</select><div class="invalid-feedback">{{ __("fields.commodity.material_type") }} را انتخاب کنید</div></div><div class="form-group col-md-3"><label for="material_amount">{{ __("fields.commodity.material_amount") }}</label><input type="number" step="0.00001" name="material_amount[0]" class="form-control"id="material_amount"placeholder="{{ __("fields.commodity.material_amount") }}" min="0.00001" ' + requiredAttr + '><div class="invalid-feedback">لطفاً {{ __("fields.commodity.material_amount") }} را وارد کنید</div></div><div class="form-group col-md-2"><label for="material_unit">{{ __("fields.unit") }}</label><select name="material_units[0]" class="form-control material-unit-select" ' + requiredAttr + '><option value="">انتخاب کنید...</option></select><div class="invalid-feedback">واحد را انتخاب کنید</div></div><div class="form-group col-sm-auto"><label for="" class="d-none d-md-block">&nbsp;</label><button type="button" class="btn btn-danger btn-block py-2 remove-row-btn">حذف</button></div></div>';

        $('#newRow').append(html);

        document.querySelectorAll('#inputFormRow').forEach((element, index) => {
            element.querySelector('select[name^="materials"]').setAttribute('name', 'materials[' + index + ']');
            element.querySelector('input[name^="material_amount"]').setAttribute('name', 'material_amount[' + index + ']');
            element.querySelector('select[name^="material_units"]').setAttribute('name', 'material_units[' + index + ']');
        });
    });

    // Remove row functionality
    $(document).on('click', '.remove-row-btn, #removeRow', function () {
        $(this).closest('#inputFormRow').remove();
        document.querySelectorAll('#inputFormRow').forEach((element, index) => {
            element.querySelector('select[name^="materials"]').setAttribute('name', 'materials[' + index + ']');
            element.querySelector('input[name^="material_amount"]').setAttribute('name', 'material_amount[' + index + ']');
            element.querySelector('select[name^="material_units"]').setAttribute('name', 'material_units[' + index + ']');
        });
    });

    // Input formatting
    $('input[name="warning_limit"]').on('change keyup paste', function(){
        $('input[name="warning_limit"]').val(Math.floor(this.value));
    });

    $('input[name="purchase_price"]').on('change keyup paste', function(){
        $('input[name="purchase_price"]').val(Math.floor(this.value));
    });
</script>

