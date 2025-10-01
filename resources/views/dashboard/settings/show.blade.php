@extends('layouts.main')
@section('title', 'نمایش تنظیمات')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزئیات تنظیمات: {{ $setting->name }}</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111">نام:</label>
                                <input type="text" value="{{ $setting->name }}" class="form-control"
                                    id="exampleInputEmail111" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111">کلید:</label>
                                <input type="text" value="{{ $setting->key }}" class="form-control"
                                    id="exampleInputEmail111" disabled>
                            </div>
                        </div>

                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111">نوع:</label>
                                <input type="text" value="{{ $setting->type }}" class="form-control"
                                    id="exampleInputEmail111" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111">وضعیت:</label>
                                <input type="text" value="{{ $setting->is_active ? 'فعال' : 'غیرفعال' }}" class="form-control"
                                    id="exampleInputEmail111" disabled>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail111">مقدار:</label>
                            <input type="text" value="{{ $setting->value }}" class="form-control"
                                id="exampleInputEmail111" disabled>
                        </div>

                        @if($setting->description)
                            <div class="form-group col-md-12">
                                <label for="exampleInputEmail111">توضیحات:</label>
                                <textarea class="form-control" id="exampleInputEmail111" disabled>{{ $setting->description }}</textarea>
                            </div>
                        @endif

                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111">تاریخ ایجاد:</label>
                                <input type="text" value="{{ $setting->created_at->format('Y/m/d H:i:s') }}" class="form-control"
                                    id="exampleInputEmail111" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111">آخرین به‌روزرسانی:</label>
                                <input type="text" value="{{ $setting->updated_at->format('Y/m/d H:i:s') }}" class="form-control"
                                    id="exampleInputEmail111" disabled>
                            </div>
                        </div>

                        <div class="form-row col-md-12">
                            <div class="col-md-12 text-center">
                                <a href="{{ route('settings.edit', $setting) }}" class="btn btn-primary">ویرایش</a>
                                <a href="{{ route('settings.index') }}" class="btn btn-secondary">بازگشت</a>
                            </div>
                        </div>
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
