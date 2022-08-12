@extends('layouts.main')
@section('title', 'گزارش خرید')

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/imexport-print.css')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/daterange-picker.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">گزارش خرید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('importing.report.store') }}" class="needs-validation" novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
                                    <select id="commodity_id" class="form-control" name="commodity_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($commodities as $commodity)
                                            <option value="{{ $commodity->id }}">{{$commodity->title}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ __('fields.commodity.name')}} را انتخاب کنید
                                    </div>
                                </div>

                                    <div class="form-group col-md-4">
                                        <label>{{ __('fields.date_from') }}</label>
                                        <input type="text" name="date_from" id="date_from" class="form-control usage"
                                               autocomplete="off" required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{  __('fields.date_from') }} را وارد کنید.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('fields.date_to') }}</label>
                                        <input type="text" name="date_to" id="date_to" class="form-control usage"
                                               autocomplete="off" required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{  __('fields.date_to') }} را وارد کنید.
                                        </div>
                                    </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">مشاهده گزارش</button>
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
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/daterange-picker.js') }}"></script>
@endsection
