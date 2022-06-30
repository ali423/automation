@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش انبار</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('customer.update', $customer) }}" class="needs-validation"
                            novalidate="">
                            @method('PATCH')
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="name"> {{ __('fields.full_name') }}</label>
                                    <input type="text" name="name" value="{{ $customer->name }}" class="form-control"
                                           id="name" placeholder=" {{ __('fields.full_name') }}" required="">
                                    <div class="invalid-feedback">
                                        لطفاً {{ __('fields.full_name') }}  را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="name"> {{ __('fields.mobile') }}</label>
                                    <input type="text" name="mobile" value="{{ $customer->mobile }}" class="form-control"
                                           id="name" placeholder=" {{ __('fields.mobile') }}" required="">
                                    <div class="invalid-feedback">
                                        لطفاً {{ __('fields.mobile') }}  را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="name"> {{ __('fields.comp_name') }}</label>
                                    <input type="text" name="comp_name" value="{{ $customer->comp_name }}" class="form-control"
                                           id="name" placeholder=" {{ __('fields.comp_name') }}" >
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="address"> {{ __('fields.address') }}</label>
                                    <input type="text" name="address" value="{{ $customer->address }}" class="form-control"
                                           id="address" placeholder=" {{ __('fields.address') }}"  required="">
                                    <div class="invalid-feedback">
                                        لطفاً {{ __('fields.address') }}  را وارد کنید.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="zip_code"> {{ __('fields.zip_code') }}</label>
                                    <input type="text" name="zip_code" value="{{ $customer->zip_code }}" class="form-control"
                                           id="zip_code" placeholder=" {{ __('fields.zip_code') }}" >
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="phone"> {{ __('fields.phone') }}</label>
                                    <input type="text" name="phone" value="{{ $customer->phone }}" class="form-control"
                                           id="phone" placeholder=" {{ __('fields.phone') }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="national_code"> {{ __('fields.national_code') }}</label>
                                    <input type="text" name="national_code" value="{{ $customer->national_code }}" class="form-control"
                                           id="national_code" placeholder=" {{ __('fields.national_code') }}" >
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="economic_code"> {{ __('fields.economic_code') }}</label>
                                    <input type="text" name="economic_code" value="{{ $customer->economic_code }}" class="form-control"
                                           id="economic_code" placeholder=" {{ __('fields.economic_code') }}" >
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ویرایش</button>
                            <a href="{{ route('customer.index') }}" class="btn btn-danger">انصراف</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection

