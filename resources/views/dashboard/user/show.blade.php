@extends('layouts.main')
@section('title', 'مشخصات کاربر')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات کاربر</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.name') }}</label>
                                <input type="text" name="name" value="{{ $user->name }}" class="form-control"
                                    id="exampleInputEmail111" placeholder="{{ __('fields.name') }}" autocomplete="off"
                                    disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.lastname') }}</label>
                                <input type="text" name="name" value="{{ $user->lastname }}" class="form-control"
                                    id="exampleInputEmail111" placeholder="{{ __('fields.lastname') }}" autocomplete="off"
                                    disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.user_name') }}</label>
                                <input type="text" name="user_name" value="{{ $user->user_name }}" class="form-control"
                                    id="exampleInputEmail111" placeholder="{{ __('fields.user_name') }}"
                                    autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-6 position-relative">
                                <label for="exampleInputEmail111"> {{ __('fields.role.name') }}</label>
                                <input type="text" name="name" value="{{ $user->role->name }}" class="form-control"
                                    id="exampleInputEmail111" placeholder="{{ __('fields.role.name') }}"
                                    autocomplete="off" disabled>
                                <a href="{{ route('role.show', $user->role) }}" class="rolename-icon">
                                    <i class="ti-arrow-left"></i>
                                </a>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="warning_message" class="custom-control-input" disabled
                                           @if($user->warning_message == true)
                                               checked
                                           @endif
                                           id="warning_message">
                                    <label class="custom-control-label" for="warning_message">ارسال پیامک هشدار</label>
                                </div>
                            </div>
                            <div class="form-group col-md-6" id="mobile_div" >
                                <label for="mobile"> {{ __('fields.mobile') }}</label>
                                <input type="text" name="mobile" value="{{ $user->mobile }}" disabled
                                       class="form-control" id="mobile"
                                       placeholder="{{ __('fields.mobile') }}" >
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.status') }}</label>
                                <input type="text" name="name" value="{{ __('fields.user.status')[$user->status] }}"
                                    class="form-control" id="exampleInputEmail111"
                                    placeholder="{{ __('fields.status') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.created_at') }}</label>
                                <input type="text" name="name"
                                    value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($user->created_at)) }}"
                                    class="form-control" id="exampleInputEmail111"
                                    placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.creator') }}</label>
                                <input type="text" name="name"
                                    @if (isset($user->creator_user)) value="{{ $user->creator_user->name . ' ' . $user->creator_user->lastname }}"
                                       @else
                                       value="سیستم" @endif
                                    class="form-control" id="exampleInputEmail111"
                                    placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('user.edit', $user) }}" class="btn btn-primary">ویرایش</a>

                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('reset-password.store', $user) }}" class="btn btn-success px-2 px-md-4 my-1 m-md-0">تغییر
                                    کلمه
                                    عبور</a>
                                <a href="{{ route('activity.index', [
                                    'object_id' => $user->id,
                                    'object_type' => class_basename($user),
                                ]) }}"
                                    class="btn btn-dfprimary px-2 px-md-4 m-md-0">تاریخچه تغییرات</a>
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
