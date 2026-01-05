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
                <button type="button" class="factor factorbtn btn btn-secondary m-1" onclick="printProformaInvoice('proforma-invoice-2')">
                    <i class="ti-printer font-18"></i> چاپ پیش فاکتور
                </button>
                <button type="button" class="btn btn-secondary m-1" onclick="showProformaInvoice('proforma-invoice-2')">
                    <i class="ti-eye font-18"></i> نمایش پیش فاکتور
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Proforma Invoice Section -->
<div id="proforma-invoice-close" style="display:none; position: fixed; top: 10px; left: 10px; z-index: 10001;">
    <button type="button" class="btn btn-danger btn-sm" onclick="hideProformaInvoice('proforma-invoice-2')">بستن</button>
</div>
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
                        <table class="table customerspecs" style="font-size: 11px; margin-bottom: 5px;">
                            <tbody>
                            <tr>
                                <td class="text-left">
                                     نام خریدار: <span>{{ $order->customer ? $order->customer->name.'-'.($order->customer->comp_name ?? '') : ''}} </span></td>
                                <td>شماره اقتصادی: {{$order->customer->economic_code ?? ''}}</td>
                                <td>شماره ملی: {{$order->customer->national_code ?? ''}}</td>
                            </tr>
                            <tr>
                                <td class="text-left">استان: <span>{{ $order->customer->province ?? '' }}</span></td>
                                <td>شهرستان: {{ $order->customer->city ?? '' }}</td>
                                <td>کدپستی: {{$order->customer->zip_code ?? ''}}</td>
                            </tr>
                            <tr>
                                <td class="text-left">آدرس: <span>{{$order->customer->address ?? ''}} </span></td>
                                <td>شهر: {{ $order->customer->city ?? '' }}</td>
                                <td>تلفن: {{$order->customer->mobile ?? ''}}</td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="factortable table table-bordered text-center mt-3" style="font-size: 11px; margin-bottom: 5px;">
                            <thead>
                            <tr class="table-secondary">
                                <th scope="col">ردیف</th>
                                <th scope="col">کد کالا</th>
                                <th scope="col">نام کالا</th>
                                <th scope="col">تعداد / مقدار</th>
                                <th scope="col">واحد</th>
                                <th scope="col">وزن (کیلوگرم)</th>
                                <th scope="col">تعداد کارتن</th>
                                <th scope="col">تعداد در کارتن</th>
                                <th scope="col">مقدار اضافی</th>
                                <th scope="col">فی</th>
                                <th scope="col">جمع کل</th>
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
                                    <td>
                                        @if($item->weight_kg !== null)
                                            {{ number_format($item->weight_kg, 0) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($order->box_quantities[$item->commodity_id]) && $order->box_quantities[$item->commodity_id]['can_calculate'])
                                            {{ $order->box_quantities[$item->commodity_id]['boxes'] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($order->box_quantities[$item->commodity_id]) && $order->box_quantities[$item->commodity_id]['can_calculate'])
                                            {{ $order->box_quantities[$item->commodity_id]['pieces_per_box'] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($order->box_quantities[$item->commodity_id]) && $order->box_quantities[$item->commodity_id]['can_calculate'])
                                            {{ $order->box_quantities[$item->commodity_id]['remaining_pieces'] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->price) }}</td>
                                    <td>{{ number_format($item->total_price_with_vat) }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="11" class="text-left" style="vertical-align: top">
                                        <div class="d-flex justify-content-between">
                                            <span>شرایط و نحوه تسویه: </span>
                                            <span>نقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                            <span>غیرنقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                        </div>
                                        <p>توضیحات:</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="text-left">
                                        <div class="d-flex justify-content-between">
                                            <span>جمع کل : {{ number_format($order->total_price_with_vat) }}</span>
                                            <span>وزن کل : {{ $order->total_weight_kg !== null ? number_format($order->total_weight_kg, 0) . ' کیلوگرم' : 'نامشخص' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="text-left">جمع کل به حروف: 
                                        @php
                                            use NumberToWords\NumberToWords;
                                            $numberToWords = NumberToWords::transformNumber('fa', $order->total_price_with_vat);
                                        @endphp
                                        {{ $numberToWords }} ریال
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="text-left" style="height: 80px">مهر و امضای فروشنده:</td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="text-left" style="height: 80px">مهر و امضای خریدار:</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
