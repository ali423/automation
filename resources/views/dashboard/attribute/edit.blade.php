@extends('layouts.main')
@section('title', 'ویرایش ویژگی')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش ویژگی</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('attribute.update', $attribute) }}" class="needs-validation" novalidate>
                            @csrf
                            @method('PATCH')
                            @include('dashboard.attribute.partials.form-fields')

                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary mr-2">بروزرسانی</button>
                                <a href="{{ route('attribute.index') }}" class="btn btn-danger">انصراف</a>
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
@endsection
