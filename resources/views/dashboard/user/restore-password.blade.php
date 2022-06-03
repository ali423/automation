@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش کاربر</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('reset-password.store', $user) }}" class="needs-validation"
                              novalidate="">
                            @method('PATCH')
                            @csrf
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail111"> {{  __('fields.password') }}</label>
                                    <input type="password" name="password" class="form-control"
                                           id="exampleInputEmail111" placeholder="{{  __('fields.password') }}"
                                           autocomplete="off"
                                    ></div>
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail111"> {{  __('fields.c_password') }}</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                           id="exampleInputEmail111" placeholder="{{  __('fields.c_password') }}"
                                           autocomplete="off"
                                    >
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
@endsection

