@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">تغییر کلمه عبور</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('reset-password.store', $user) }}" class="needs-validation"
                            novalidate="">
                            @method('PATCH')
                            @csrf
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail111"> {{ __('fields.password') }}</label>
                                    <input type="password" name="password" class="form-control" id="userpass"
                                        placeholder="{{ __('fields.password') }}" autocomplete="off"
                                        pattern="[a-zA-Z0-9]+" minlength="8" maxlength="16" required>
                                    <div class="invalid-feedback">این فیلد حداقل باید از 8 کاراکتر تشکیل شده باشد. (از حروف
                                        انگلیسی استفاده کنید)</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail111"> {{ __('fields.c_password') }}</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        id="userpass_confirmation" placeholder="{{ __('fields.c_password') }}"
                                        autocomplete="off" minlength="8" maxlength="16" required>
                                    <div class="invalid-feedback">رمز عبور هم خوانی ندارد.</div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">ویرایش</button>
                            <a href="{{ route('user.index') }}" class="btn btn-danger">انصراف</a>
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
    <script src="{{ asset('js/passConfirmation.js') }}"></script>
@endsection
