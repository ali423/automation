@extends('layouts.main')
@section('title', __('fields.edit_conversion'))

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">{{ __('fields.edit_conversion') }}</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('unit-conversion.update', $unitConversion) }}" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="commodity_id">{{ __('fields.commodity.name') }}</label>
                                    <select name="commodity_id" id="commodity_id" class="form-control @error('commodity_id') is-invalid @enderror" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach($commodities as $commodity)
                                            <option value="{{ $commodity->id }}" data-unit-id="{{ $commodity->unit_id }}" data-unit-name="{{ $commodity->unit ? $commodity->unit->name : '' }}" data-unit-symbol="{{ $commodity->unit ? $commodity->unit->symbol : '' }}" {{ old('commodity_id', $unitConversion->commodity_id) == $commodity->id ? 'selected' : '' }}>
                                                {{ $commodity->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('commodity_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="from_unit_id">{{ __('fields.from_unit') }}</label>
                                    <input type="text" id="from_unit_display" class="form-control" value="" readonly>
                                    <input type="hidden" name="from_unit_id" id="from_unit_id" value="">
                                    <small class="form-text text-muted">
                                        <i class="ti-info-circle"></i> واحد پایه کالا به عنوان واحد مبدا انتخاب می‌شود
                                    </small>
                                    @error('from_unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="to_unit_id">{{ __('fields.to_unit') }}</label>
                                    <select name="to_unit_id" id="to_unit_id" class="form-control @error('to_unit_id') is-invalid @enderror" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ (old('to_unit_id', $unitConversion->to_unit_id) == $unit->id) ? 'selected' : '' }}>
                                                {{ $unit->name }} ({{ $unit->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('to_unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="conversion_rate">{{ __('fields.conversion_rate') }}</label>
                                    <input type="number" 
                                           name="conversion_rate" 
                                           id="conversion_rate" 
                                           class="form-control @error('conversion_rate') is-invalid @enderror" 
                                           step="0.01" 
                                           min="0.01" 
                                           value="{{ old('conversion_rate', $unitConversion->conversion_rate) }}" 
                                           placeholder="مثال: 1000"
                                           required>
                                    <small class="form-text text-muted">
                                        {{ __('fields.example_kg_to_g') }}
                                    </small>
                                    @error('conversion_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <i class="ti-info-circle"></i>
                                <strong>{{ __('fields.conversion_help') }}</strong>
                                <ul class="mb-0 mt-2">
                                    <li>{{ __('fields.example_kg_to_g') }}</li>
                                    <li>{{ __('fields.example_g_to_kg') }}</li>
                                </ul>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary mr-2">بروزرسانی</button>
                                <a href="{{ route('unit-conversion.index') }}" class="btn btn-danger">انصراف</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        // Prevent selecting same unit for both from and to
        $('#from_unit_id, #to_unit_id').on('change', function() {
            var fromUnit = $('#from_unit_id').val();
            var toUnit = $('#to_unit_id').val();
            if (fromUnit && toUnit && fromUnit === toUnit) {
                alert('واحد مبدا و مقصد نمی‌توانند یکسان باشند');
                $(this).val('');
            }
        });
        // Set from_unit_id and display when commodity changes
        function updateFromUnit() {
            var selected = $('#commodity_id').find('option:selected');
            var unitId = selected.data('unit-id');
            var unitName = selected.data('unit-name');
            var unitSymbol = selected.data('unit-symbol');
            $('#from_unit_id').val(unitId || '');
            if(unitName && unitSymbol) {
                $('#from_unit_display').val(unitName + ' (' + unitSymbol + ')');
            } else {
                $('#from_unit_display').val('');
            }
            // Optionally, remove the selected from_unit from to_unit options
            $('#to_unit_id option').show();
            if(unitId) {
                $('#to_unit_id option[value="' + unitId + '"]').hide();
                if($('#to_unit_id').val() == unitId) {
                    $('#to_unit_id').val('');
                }
            }
        }
        $('#commodity_id').on('change', updateFromUnit);
        // Trigger change on page load if old value exists or for edit
        updateFromUnit();
    </script>
@endsection