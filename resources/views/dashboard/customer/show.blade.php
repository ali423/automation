@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات مشتری</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="name"> {{ __('fields.full_name') }}</label>
                                <input type="text" name="name" value="{{ $customer->name }}" class="form-control"
                                       id="name" placeholder=" {{ __('fields.full_name') }}" disabled>
                                <div class="invalid-feedback">
                                    لطفاً {{ __('fields.full_name') }}  را وارد کنید.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name"> {{ __('fields.mobile') }}</label>
                                <input type="text" name="mobile" value="{{ $customer->mobile }}" class="form-control"
                                       id="name" placeholder=" {{ __('fields.mobile') }}" disabled>
                                <div class="invalid-feedback">
                                    لطفاً {{ __('fields.mobile') }}  را وارد کنید.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name"> {{ __('fields.comp_name') }}</label>
                                <input type="text" name="comp_name" value="{{ $customer->comp_name }}" class="form-control"
                                       id="name" placeholder=" {{ __('fields.comp_name') }}" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-12">
                                <label for="address"> {{ __('fields.address') }}</label>
                                <input type="text" name="address" value="{{ $customer->address }}" class="form-control"
                                       id="address" placeholder=" {{ __('fields.address') }}"  disabled>
                                <div class="invalid-feedback">
                                    لطفاً {{ __('fields.address') }}  را وارد کنید.
                                </div>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label for="zip_code"> {{ __('fields.zip_code') }}</label>
                                <input type="text" name="zip_code" value="{{ $customer->zip_code }}" class="form-control"
                                       id="zip_code" placeholder=" {{ __('fields.zip_code') }}" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="phone"> {{ __('fields.phone') }}</label>
                                <input type="text" name="phone" value="{{ $customer->phone }}" class="form-control"
                                       id="phone" placeholder=" {{ __('fields.phone') }}" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="national_code"> {{ __('fields.national_code') }}</label>
                                <input type="text" name="national_code" value="{{ $customer->national_code }}" class="form-control"
                                       id="national_code" placeholder=" {{ __('fields.national_code') }}" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="economic_code"> {{ __('fields.economic_code') }}</label>
                                <input type="text" name="economic_code" value="{{ $customer->economic_code }}" class="form-control"
                                       id="economic_code" placeholder=" {{ __('fields.economic_code') }}" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.created_at') }}</label>
                                <input type="text" name="name"
                                       value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($customer->created_at)) }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.creator') }}</label>
                                <input type="text" name="name"
                                       @if (isset($customer->creator_user)) value="{{ $customer->creator_user->full_name }}"
                                       @else
                                       value="سیستم" @endif
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('customer.edit', $customer) }}" class="btn btn-primary">ویرایش</a>
                                <form method="post" action="{{ route('customer.destroy', $customer) }}" class="d-inline w-50">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('آیا از حذف این مشتری مطمئن هستید؟');">حذف مشتری</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('activity.index', [
                                    'object_id' => $customer->id,
                                    'object_type' => class_basename($customer),
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
