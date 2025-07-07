@extends('layouts.main')
@section('title', 'ویرایش واحد')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش واحد</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('unit.update', $unit) }}" class="needs-validation" novalidate>
                            @csrf
                            @method('PATCH')
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __('fields.name') }}</label>
                                    <input type="text" name="name" value="{{ old('name', $unit->name) }}" class="form-control" id="name" required>
                                    <div class="invalid-feedback">لطفاً نام واحد را وارد کنید.</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="symbol">{{ __('fields.symbol') }}</label>
                                    <input type="text" name="symbol" value="{{ old('symbol', $unit->symbol) }}" class="form-control" id="symbol" required>
                                    <div class="invalid-feedback">لطفاً نماد واحد را وارد کنید.</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary mr-2">بروزرسانی</button>
                                <a href="{{ route('unit.index') }}" class="btn btn-danger">انصراف</a>
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
        // Debug: Log form submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('button[type="submit"]');
            
            console.log('Form found:', form);
            console.log('Submit button found:', submitBtn);
            
            form.addEventListener('submit', function(e) {
                console.log('Form submitted!');
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                
                // Check if form is valid
                if (!form.checkValidity()) {
                    console.log('Form validation failed');
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    console.log('Form validation passed, submitting...');
                }
                
                form.classList.add('was-validated');
            });
            
            submitBtn.addEventListener('click', function(e) {
                console.log('Submit button clicked!');
            });
        });
    </script>
@endsection 