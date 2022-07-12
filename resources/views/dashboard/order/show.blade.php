@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات سفارش</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="customer_id">{{ __('fields.customer')}}</label>
                                <input type="text"  class="form-control" value="{{ $order->customer->name }}" disabled>
                                <div class="invalid-feedback">
                                    {{ __('fields.customer')}} را انتخاب کنید
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
                                <input type="text"  class="form-control" value="{{ $order->commodity->title }}" disabled>
                                <div class="invalid-feedback">
                                    {{ __('fields.commodity.name')}} را انتخاب کنید
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="amount"> {{  __('fields.sell-price_per_unit') }}</label>
                                <input type="text"  class="form-control" value="{{ number_format($order->price) }}" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>{{ __('fields.deadline') }}</label>
                              <input type="text"  class="form-control" value="{{ date('Y/m/d', strtotime($order->deadline)) }}" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                                <input type="text"   class="form-control" value="{{ number_format($order->commodity_amount) }}" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="unit"> {{ __('fields.unit') }}</label>
                                <input type="text"   class="form-control" value="{{ __('fields.commodity.units')[$order->unit] }}" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="unit"> {{ __('fields.status') }}</label>
                                <input type="text"   class="form-control" value="{{ __('fields.order.status')[$order->status] }}" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.created_at') }}</label>
                                <input type="text" name="name"
                                       value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($order->created_at)) }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.creator') }}</label>
                                <input type="text" name="name"
                                       @if (isset($order->creator_user)) value="{{ $order->creator_user->full_name }}"
                                       @else
                                       value="سیستم" @endif
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('order.edit', $order) }}" class="btn btn-primary">ویرایش</a>
                                <form method="post" action="{{ route('order.destroy', $order) }}" class="d-inline w-50">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('آیا از حذف این سفارش مطمئن هستید؟');">حذف سفارش</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('order.confirm', $order) }}" class="btn btn-success">تحویل سفارش</a>
                                <a href="{{ route('activity.index', [
                                    'object_id' => $order->id,
                                    'object_type' => class_basename($order),
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
