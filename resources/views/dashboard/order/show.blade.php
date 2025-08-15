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
                            <div class="form-group col-md-6">
                                <label for="customer_id">{{ __('fields.customer')}}</label>
                                <input type="text" class="form-control" value="{{ $order->customer ? $order->customer->name : 'مشتری حذف شده' }}" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ __('fields.deadline') }}</label>
                                <input type="text" class="form-control" value="{{ $order->deadline }}" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="unit"> {{ __('fields.status') }}</label>
                                <input type="text" class="form-control" value="{{ __('fields.order.status')[$order->status] }}" disabled>
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
                                       value="سیستم"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                        </div>

                        <!-- Order Items -->
                        @if($order->orderItems->count() > 0)
                            <div class="form-row mt-4">
                                <div class="col-12">
                                    <h5>جزئیات سفارش</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ردیف</th>
                                                    <th>کالا</th>
                                                    <th>مقدار</th>
                                                    <th>واحد</th>
                                                    <th>قیمت واحد</th>
                                                    <th>قیمت کل</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->orderItems as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $item->commodity ? $item->commodity->title : 'کالا حذف شده' }}</td>
                                                        <td>{{ number_format($item->commodity_amount) }}</td>
                                                        <td>{{ $item->unit_symbol }}</td>
                                                        <td>{{ number_format($item->price) }} تومان</td>
                                                        <td>{{ number_format($item->total_price) }} تومان</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="5" class="text-left">مجموع کل:</th>
                                                    <th>{{ number_format($order->total_price) }} تومان</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
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
                    <button type="button" class="factor factorbtn btn btn-secondary m-1" onclick="printProformaInvoice('proforma-invoice')">
                        <i class="ti-printer font-18"></i> چاپ پیش فاکتور
                    </button>
                    <button type="button" class="factor factorbtn btn btn-secondary m-1" onclick="printProformaInvoice('proforma-invoice-2')">
                        <i class="ti-printer font-18"></i> چاپ پیش فاکتور
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Hidden Proforma Invoice Section 1 -->
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
                                    <th scope="col" colspan="2">جمع کل + مالیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->commodity ? $item->commodity->number : '' }}</td>
                                        <td>{{ $item->commodity ? $item->commodity->title : '' }}</td>
                                        <td>{{ number_format($item->commodity_amount) }}</td>
                                        <td>{{ $item->unit_symbol }}</td>
                                        <td>{{ number_format($item->price) }}</td>
                                        <td colspan="2">{{ number_format($item->total_price) }}</td>
                                    </tr>
                                    @endforeach
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
                                        <td colspan="2" class="text-left">جمع کل : {{ number_format($order->total_price) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-left">جمع کل به حروف: {{ $order->total_price }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left" style="height: 120px">مهر و امضای فروشنده:</td>
                                        <td colspan="3" class="text-left">مهر و امضای خریدار:</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hidden Proforma Invoice Section 2 (Duplicate for customization) -->
    <div id="proforma-invoice-2" style="display:none;">
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
                                    <th scope="col">تعداد کارتن</th>
                                    <th scope="col">تعداد در کارتن</th>
                                    <th scope="col">فی</th>
                                    <th scope="col">جمع کل + مالیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->commodity ? $item->commodity->number : '' }}</td>
                                        <td>{{ $item->commodity ? $item->commodity->title : '' }}</td>
                                        <td>{{ number_format($item->commodity_amount) }}</td>
                                        <td>{{ $item->unit_symbol }}</td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ number_format($item->price) }}</td>
                                        <td>{{ number_format($item->total_price) }}</td>
                                    </tr>
                                    @endforeach
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
                                        <td colspan="4" class="text-left">جمع کل : {{ number_format($order->total_price) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-left">جمع کل به حروف: {{ $order->total_price }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left" style="height: 120px">مهر و امضای فروشنده:</td>
                                        <td colspan="4" class="text-left">مهر و امضای خریدار:</td>
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
        function printProformaInvoice(invoiceId) {
            var printContents = document.getElementById(invoiceId).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            setTimeout(function() {
                document.body.innerHTML = originalContents;
            }, 100);
        }
    </script>
@endsection
