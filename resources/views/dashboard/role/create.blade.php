@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">نقش جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('role.store') }}" class="needs-validation" novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail111"> نام نقش (فارسی)</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                        id="exampleInputEmail111" placeholder="حسابدار" autocomplete="off" required="">
                                    <div class="invalid-feedback">
                                        لطفاً نام نقش را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail111"> عنوان نقش (انگلیسی)</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                        id="exampleInputEmail111" placeholder="accountant" pattern="[a-zA-Z]+" required="">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان نقش را وارد کنید.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row col-md-12">
                                    @foreach ($permissions as $permission)
                                        <div class="col-md-3">
                                            <div class="form-group"><input type="checkbox"
                                                    @if (old('permissions') && in_array($permission->id, old('permissions'))) checked @endif name="permissions[]"
                                                    value="{{ $permission->id }}" class="">
                                                {{ $permission->name }} </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ایجاد نقش</button>
                            <a href="{{ route('role.index') }}" class="btn btn-danger">انصراف</a>
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
