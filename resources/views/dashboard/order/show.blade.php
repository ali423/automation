@extends('layouts.main')
@section('title', 'نمایش سفارش')

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
                                <input type="text"  class="form-control" value="{{ $order->customer ? $order->customer->name : 'مشتری حذف شده' }}" disabled>
                                <div class="invalid-feedback">
                                    {{ __('fields.customer')}} را انتخاب کنید
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="commodity_id">{{ __('fields.commodity.name')}}</label>
                                <input type="text"  class="form-control" value="{{ $order->commodity ? $order->commodity->title : 'کالا حذف شده' }}" disabled>
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
                        @if($order->status =='done')
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('fields.done_date') }}</label>
                                    <input type="text"  class="form-control" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($order->updated_at)) }}" disabled>
                                </div>
                            </div>
                            @endif

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
    <!-- Proforma Invoice Download Button -->
    <div class="row mt-2">
    </div>
    <!-- Proforma Invoice Print Card -->
    <div class="col-xl-12 height-card box-margin">
        <div class="card">
            <div class="card-body">
                <div class="bg-transparent d-flex align-items-center justify-content-between">
                    <div class="widgets-card-title">
                        <h5 class="card-title">چاپ پیش فاکتور</h5>
                    </div>
                </div>
                <div class="d-md-flex justify-content-center">
                    <button type="button" class="factor factorbtn btn btn-secondary m-1" onclick="printProformaInvoice()">
                        <i class="ti-printer font-18"></i>چاپ پیش فاکتور
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Hidden Proforma Invoice Section -->
    <div id="proforma-invoice" style="display:none;">
        <div class="row mt-4">
            <div class="col-xl-12 box-margin height-card">
                <div class="card card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="d-flex justify-content-between">
                                <div class="logo"><img src="{{ asset('img/logo/darklogo.png') }}" style="width: 120px; height: auto;"/></div>
                                <div><h4>پیش فاکتور</h4></div>
                                <div>
                                    <p>شماره پیش فاکتور: <span>{{$order->id}}</span></p>
                                    <p>تاریخ: <span>{{\Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($order->created_at))}}</span></p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center border mt-3 mb-2">
                                <div class="text-dark p-1">مشخصات خریدار</div>
                            </div>
                            <table class="table customerspecs">
                                <tbody>
                                <tr>
                                    <td class="text-left">
                                         نام خریدار: <span>{{ $order->customer ? $order->customer->name.'-'.($order->customer->comp_name ?? '') : ''}} </span></td>
                                    <td></td>
                                    <td></td>
                                    <td>شماره اقتصادی: {{$order->customer->economic_code ?? ''}}</td>
                                    <td></td>
                                    <td> شماره ملی:{{ $order->customer->national_code ?? ''}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-left">استان: <span> </span></td>
                                    <td>شهرستان:</td>
                                    <td></td>
                                    <td> کدپستی:{{$order->customer->zip_code ?? ''}}</td>
                                    <td></td>
                                    <td>شهر:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-left">آدرس: <span>{{$order->customer->address ?? ''}} </span></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>تلفن: {{$order->customer->mobile ?? ''}}</td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="factortable table table-bordered text-center mt-3">
                                <thead>
                                <tr class="table-secondary">
                                    <th scope="col">ردیف</th>
                                    <th scope="col">کد کالا</th>
                                    <th scope="col">نام کالا</th>
                                    <th scope="col">تعداد / مقدار</th>
                                    <th scope="col">واحد</th>
                                    <th scope="col">فی</th>
                                    <th scope="col">جمع کل</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>{{ $order->commodity ? $order->commodity->number : '' }}</td>
                                        <td>{{ $order->commodity ? $order->commodity->title : '' }}</td>
                                        <td>{{ number_format($order->commodity_amount) }}</td>
                                        <td>{{ __('fields.commodity.units')[$order->unit] }}</td>
                                        <td>{{ number_format($order->price) }}</td>
                                        <td>{{ number_format($order->price * $order->commodity_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" rowspan="3" class="text-left" style="vertical-align: top">
                                            <div class="d-flex justify-content-between">
                                                <span>شرایط و نحوه تسویه: </span>
                                                <span>نقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                                <span>غیرنقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                            </div>
                                            <p>توضیحات:</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right">جمع کل</td>
                                        <td>{{ number_format($order->price * $order->commodity_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-left">جمع کل به حروف: {{ \NumberToWords\NumberToWords::transformNumber('fa', $order->price * $order->commodity_amount) ?? '' }} ریال </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left" style="height: 120px">مهر و امضای فروشنده:</td>
                                        <td colspan="2" class="text-left">مهر و امضای خریدار:</td>
                                    </tr>
                                </tbody>
                            </table>
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
    <script>
        function printProformaInvoice() {
            var printContents = document.getElementById('proforma-invoice').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
@endsection
