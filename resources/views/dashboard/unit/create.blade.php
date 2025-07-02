@extends('layouts.main')
@section('title', 'افزودن واحد جدید')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">واحد جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('unit.store') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __('fields.name') }}</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" id="name" placeholder="نام واحد (مثل کیلوگرم)" required>
                                    <div class="invalid-feedback">لطفاً نام واحد را وارد کنید.</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="symbol">{{ __('fields.symbol') }}</label>
                                    <input type="text" name="symbol" value="{{ old('symbol') }}" class="form-control" id="symbol" placeholder="نماد واحد (مثل kg)" required>
                                    <div class="invalid-feedback">لطفاً نماد واحد را وارد کنید.</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ثبت واحد</button>
                            <a href="{{ route('unit.index') }}" class="btn btn-danger">انصراف</a>
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
    </script>
@endsection