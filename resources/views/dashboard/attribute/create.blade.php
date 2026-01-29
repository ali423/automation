@extends('layouts.main')
@section('title', 'افزودن ویژگی جدید')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویژگی جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('attribute.store') }}" class="needs-validation" novalidate>
                            @csrf
                            @include('dashboard.attribute.partials.form-fields')

                            <button type="submit" class="btn btn-primary mr-2">ثبت ویژگی</button>
                            <a href="{{ route('attribute.index') }}" class="btn btn-danger">انصراف</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection
