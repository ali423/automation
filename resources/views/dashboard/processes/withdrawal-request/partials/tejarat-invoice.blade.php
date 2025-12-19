<!-- Tejarat Invoice Template -->
<style>
    @media print {
        @page {
            size: landscape;
        }
        #finvoice-tejarat {
            font-size: 9px !important;
        }
        #finvoice-tejarat .factortable {
            border: 2px solid #000 !important;
        }
        #finvoice-tejarat .factortable th,
        #finvoice-tejarat .factortable td {
            border: 2px solid #000 !important;
            font-size: 9px !important;
            padding: 4px !important;
            color: #000 !important;
        }
        #finvoice-tejarat .sellerspecs,
        #finvoice-tejarat .customerspecs {
            border: 2px solid #000 !important;
        }
        #finvoice-tejarat .sellerspecs td,
        #finvoice-tejarat .sellerspecs th,
        #finvoice-tejarat .customerspecs td,
        #finvoice-tejarat .customerspecs th {
            border: 2px solid #000 !important;
            font-size: 9px !important;
            padding: 4px !important;
            color: #000 !important;
        }
        #finvoice-tejarat h4 {
            font-size: 11px !important;
        }
        #finvoice-tejarat p {
            font-size: 9px !important;
            margin-bottom: 2px !important;
        }
        #finvoice-tejarat .mb-3 {
            margin-bottom: 4px !important;
        }
        #finvoice-tejarat .border {
            margin-bottom: 2px !important;
        }
        #finvoice-tejarat .card-body {
            padding: 4px !important;
        }
        #finvoice-tejarat .d-flex {
            margin-bottom: 2px !important;
        }
        #finvoice-tejarat .factortable td[style*="height"] {
            height: 45px !important;
            padding: 3px !important;
        }
    }
    @media screen {
        #finvoice-tejarat {
            font-size: 10px;
        }
        #finvoice-tejarat .factortable th,
        #finvoice-tejarat .factortable td {
            font-size: 10px;
            color: #000 !important;
        }
        #finvoice-tejarat .sellerspecs td,
        #finvoice-tejarat .sellerspecs th,
        #finvoice-tejarat .customerspecs td,
        #finvoice-tejarat .customerspecs th {
            font-size: 10px;
            color: #000 !important;
        }
    }
    #finvoice-tejarat .factortable {
        border-collapse: collapse;
        width: 100%;
        direction: rtl;
        margin-bottom: 4px !important;
    }
    #finvoice-tejarat .factortable th,
    #finvoice-tejarat .factortable td {
        text-align: center !important;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 6px 3px;
        color: #000 !important;
        line-height: 1.1;
    }
    #finvoice-tejarat .factortable thead tr {
        background-color: #f8f9fa;
    }
    #finvoice-tejarat .factortable thead th {
        padding: 6px 3px;
        font-size: 10px;
        line-height: 1.1;
        white-space: nowrap;
    }
    #finvoice-tejarat .factortable td.text-right {
        text-align: right !important;
    }
    #finvoice-tejarat .sellerspecs,
    #finvoice-tejarat .customerspecs {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 4px !important;
    }
    #finvoice-tejarat .sellerspecs td,
    #finvoice-tejarat .sellerspecs th,
    #finvoice-tejarat .customerspecs td,
    #finvoice-tejarat .customerspecs th {
        border: 1px solid #dee2e6;
        padding: 6px 3px;
        color: #000 !important;
        font-size: 10px;
        text-align: right !important;
        line-height: 1.1;
    }
    #finvoice-tejarat {
        color: #000 !important;
    }
    #finvoice-tejarat * {
        color: #000 !important;
    }
    #finvoice-tejarat .mb-3 {
        margin-bottom: 4px !important;
    }
    #finvoice-tejarat .border {
        margin-bottom: 2px !important;
        padding: 1px !important;
    }
    #finvoice-tejarat h4 {
        font-size: 12px;
        margin-bottom: 2px !important;
        line-height: 1.2;
    }
    #finvoice-tejarat p {
        margin-bottom: 1px !important;
        font-size: 10px;
        line-height: 1.1;
    }
    #finvoice-tejarat .card-body {
        padding: 4px !important;
    }
    #finvoice-tejarat .d-flex {
        margin-bottom: 2px !important;
    }
    #finvoice-tejarat .factortable {
        margin-bottom: 4px !important;
    }
    #finvoice-tejarat .sellerspecs,
    #finvoice-tejarat .customerspecs {
        margin-bottom: 4px !important;
    }
    #finvoice-tejarat .row {
        margin: 0 !important;
    }
    #finvoice-tejarat .col-sm-12 {
        padding: 0 !important;
    }
</style>
<div id="finvoice-tejarat" class="col-xl-12 box-margin height-card d-none hideprint">
    <div class="card card-body">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="d-flex justify-content-between">
                    <div class="logo"><img src="{{ asset('img/logo/darklogo.png') }}"/></div>
                    <div><h4>نسخه سامانه جامع تجارت</h4></div>
                    <div>
                        <p>شماره فاکتور: <span>{{$request->number}}</span></p>
                        <p>تاریخ: <span>{{\Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at))}}</span></p>
                    </div>
                </div>
                
                <!-- Shipping and Carrier Information -->
                <div class="d-flex justify-content-center border">
                    <div class="text-dark p-1">اطلاعات حمل</div>
                </div>
                <table class="table sellerspecs">
                    <tbody>
                    <tr>
                        <td>راننده: {{ $request->driver_name ?? 'نامشخص' }}</td>
                        <td>تلفن راننده: {{ $request->driver_phone ?? '-' }}</td>
                        <td>وسیله نقلیه: {{ $request->vehicle_type ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>کد ملی راننده: {{ $request->driver_national_id ?? '-' }}</td>
                        <td>شماره بارنامه: {{ $request->bill_of_lading_number ?? '-' }}</td>
                        <td>پلاک: {{ trim(($request->plate_serial ?? '') . ' ' . ($request->plate_number ?? '')) ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td>استان: {{ $request->shipping_province ?? ($request->customer ? ($request->customer->province ?? '') : '-') }}</td>
                        <td colspan="2">شهر: {{ $request->shipping_city ?? ($request->customer ? ($request->customer->city ?? '') : '-') }}</td>
                    </tr>
                    </tbody>
                </table>
                
                <!-- Customer Information -->
                <div class="d-flex justify-content-center border">
                    <div class="text-dark p-1">مشخصات خریدار</div>
                </div>
                <table class="table customerspecs">
                    <tbody>
                    <tr>
                        <td>نام خریدار: {{ $request->customer ? $request->customer->name.'-'. ($request->customer->comp_name ?? '') : 'نامشخص' }}</td>
                        <td>شماره اقتصادی: {{$request->customer ? ($request->customer->economic_code ?? '') : ''}}</td>
                        <td>شماره ملی: {{ $request->customer ? ($request->customer->national_code ?? '') : ''}}</td>
                    </tr>
                    <tr>
                        <td>استان: {{ $request->customer ? ($request->customer->province ?? '') : '' }}</td>
                        <td>شهرستان: {{ $request->customer ? ($request->customer->city ?? '') : '' }}</td>
                        <td>کدپستی: {{$request->customer ? ($request->customer->zip_code ?? '') : ''}}</td>
                    </tr>
                    <tr>
                        <td>شهر: {{ $request->customer ? ($request->customer->city ?? '') : '' }}</td>
                        <td>تلفن: {{$request->customer ? ($request->customer->mobile ?? '') : ''}}</td>
                        <td>آدرس: {{$request->customer ? ($request->customer->address ?? '') : ''}}</td>
                    </tr>
                    </tbody>
                </table>
                
                <!-- Items Table -->
                <table class="factortable table table-bordered text-center">
                    <thead>
                    <tr class="table-secondary">
                        <th scope="col">ردیف</th>
                        <th scope="col">کد کالا</th>
                        <th scope="col">شناسه کالا</th>
                        <th scope="col">نام کالا</th>
                        <th scope="col">تعداد / مقدار</th>
                        <th scope="col">واحد</th>
                        <th scope="col">لیتراژ</th>
                        <th scope="col">فی</th>
                        <th scope="col">مالیات بر ارزش افزوده</th>
                        <th scope="col">جمع کل</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                            // Pre-calculate total price once to avoid multiple attribute calls
                            $totalPrice = $request->total_price ?? null;
                            $commodityUnitService = app(\App\Services\CommodityUnitService::class);
                        @endphp
                        @foreach($request->commodities as $commodity)
                            @php
                                // Calculate litrage: Convert amount to main unit, then multiply by litrage per unit
                                // Litrage = Amount (in main unit) × Volume per unit (litrage field)
                                $amountInMainUnit = $commodityUnitService->convertToMainUnit(
                                    $commodity,
                                    $commodity->pivot->amount,
                                    $commodity->pivot->unit_id
                                );
                                $volumePerUnit = $commodity->litrage ?? 0;
                                $totalLitrage = ($amountInMainUnit !== null && $volumePerUnit > 0) 
                                    ? $amountInMainUnit * $volumePerUnit 
                                    : null;
                                
                                // Calculate VAT amount for this commodity
                                $commodityVatAmount = 0;
                                if (isset($commodity->pivot->price)) {
                                    $commodityTotal = $commodity->pivot->amount * $commodity->pivot->price;
                                    $commodityVatAmount = $commodityTotal * vat_rate();
                                }
                            @endphp
                            <tr>
                                <td scope="row">{{ $i }}</td>
                                <td>{{ $commodity->number }}</td>
                                <td>{{ $commodity->product_identifier ?? default_product_identifier() }}</td>
                                <td>{{ $commodity->title }}</td>
                                <td>{{ number_format($commodity->pivot->amount, 0, '.', ',') }}</td>
                                <td>{{ $commodity->pivot->unit ? $commodity->pivot->unit->name : 'نامشخص' }}</td>
                                <td>{{ $totalLitrage !== null ? number_format($totalLitrage, 0, '.', ',') : '-' }}</td>
                                <td>
                                    @if(isset($commodity->pivot->price) && $commodity->litrage > 0)
                                        {{ number_format($commodity->pivot->price / $commodity->litrage) }}
                                    @else
                                        {{ isset($commodity->pivot->price) ? number_format($commodity->pivot->price) : '-' }}
                                    @endif
                                </td>
                                <td>{{ isset($commodity->pivot->price) ? number_format(round($commodityVatAmount)) : '-' }}</td>
                                <td>{{ isset($commodity->pivot->price) ? number_format(round($commodity->pivot->amount * $commodity->pivot->price * (1 + vat_rate()))) : '-' }}</td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                        @php
                            use NumberToWords\NumberToWords;
                            // Calculate VAT amount
                            $totalAmount = isset($totalPrice) && isset($totalPrice['number']) ? $totalPrice['number'] : 0;
                            $vatAmount = $totalAmount * vat_rate();
                            $totalWithVat = $totalAmount + $vatAmount;
                            // Calculate total in words
                            $totalInWords = '';
                            if(isset($totalPrice) && isset($totalPrice['world'])) {
                                $totalInWords = $totalPrice['world'];
                            } else {
                                $numberToWords = NumberToWords::transformNumber('fa', $totalWithVat);
                                $totalInWords = $numberToWords;
                            }
                        @endphp
                        <tr>
                            <td colspan="7" rowspan="5" class="text-left" style="vertical-align: top; padding: 3px !important;">
                                <div class="d-flex justify-content-between" style="margin-bottom: 2px;">
                                    <span>شرایط و نحوه تسویه: </span>
                                    <span>نقدی <span class="border" style="display:inline-block;width:10px;height:10px"></span></span>
                                    <span>غیرنقدی <span class="border" style="display:inline-block;width:10px;height:10px"></span></span>
                                </div>
                                <p style="margin-bottom: 1px;">توضیحات:</p>
                            </td>
                            <td colspan="3" class="text-left">جمع کل : {{ number_format($totalAmount) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left"> مالیات بر ارزش افزوده (%{{ number_format(vat_percentage(), 0) }}) : {{ number_format($vatAmount) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left">جمع کل با مالیات : {{ number_format($totalWithVat) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left">جمع کل به حروف: {{ $totalInWords }} ریال</td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right" style="height: 45px; padding: 3px !important;">مهر و امضای فروشنده:</td>
                            <td colspan="5" class="text-right" style="height: 45px; padding: 3px !important;">مهر و امضای خریدار:</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
