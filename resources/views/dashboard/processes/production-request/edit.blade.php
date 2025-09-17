@extends('layouts.main')
@section('title','ویرایش درخواست تولید کالا')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش درخواست تولید کالا</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('production-request.update', $request) }}" class="needs-validation" novalidate="" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            
                            @include('dashboard.processes.production-request.partials.form-fields')
                            @include('dashboard.processes.production-request.partials.materials-section')

                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default" accept="image/*,.pdf,.zip,.rar">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">انتخاب فایل</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-20">
                                <label for="comment">توضیحات</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment" rows="6">{{ old('comment', $request->description) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                                    <a href="{{ route('production-request.index') }}" class="btn btn-secondary">انصراف</a>
                                </div>
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
    @include('dashboard.processes.production-request.partials.scripts')
@endsection 