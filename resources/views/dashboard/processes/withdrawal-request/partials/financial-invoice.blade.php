<!-- Financial Invoice Template -->
<style>
    @media print {
        @page {
            size: landscape;
        }
        #finvoice2 {
            font-size: 9px !important;
        }
        #finvoice2 .factortable {
            border: 2px solid #000 !important;
        }
        #finvoice2 .factortable th,
        #finvoice2 .factortable td {
            border: 2px solid #000 !important;
            font-size: 9px !important;
            padding: 4px !important;
            color: #000 !important;
        }
        #finvoice2 .sellerspecs,
        #finvoice2 .customerspecs {
            border: 2px solid #000 !important;
        }
        #finvoice2 .sellerspecs td,
        #finvoice2 .sellerspecs th,
        #finvoice2 .customerspecs td,
        #finvoice2 .customerspecs th {
            border: 2px solid #000 !important;
            font-size: 9px !important;
            padding: 4px !important;
            color: #000 !important;
        }
        #finvoice2 h4 {
            font-size: 11px !important;
        }
        #finvoice2 p {
            font-size: 9px !important;
            margin-bottom: 2px !important;
        }
        #finvoice2 .mb-3 {
            margin-bottom: 4px !important;
        }
        #finvoice2 .border {
            margin-bottom: 2px !important;
        }
        #finvoice2 .card-body {
            padding: 4px !important;
        }
        #finvoice2 .d-flex {
            margin-bottom: 2px !important;
        }
    }
    @media screen {
        #finvoice2 {
            font-size: 10px;
        }
        #finvoice2 .factortable th,
        #finvoice2 .factortable td {
            font-size: 10px;
            color: #000 !important;
        }
        #finvoice2 .sellerspecs td,
        #finvoice2 .sellerspecs th,
        #finvoice2 .customerspecs td,
        #finvoice2 .customerspecs th {
            font-size: 10px;
            color: #000 !important;
        }
    }
    #finvoice2 .factortable {
        border-collapse: collapse;
        width: 100%;
        direction: rtl;
        margin-bottom: 4px !important;
    }
    #finvoice2 .factortable th,
    #finvoice2 .factortable td {
        text-align: center !important;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 6px 3px;
        color: #000 !important;
        line-height: 1.1;
    }
    #finvoice2 .factortable thead tr {
        background-color: #f8f9fa;
    }
    #finvoice2 .factortable thead th {
        padding: 6px 3px;
        font-size: 10px;
        line-height: 1.1;
        white-space: nowrap;
    }
    #finvoice2 .factortable td.text-right {
        text-align: right !important;
    }
    #finvoice2 .sellerspecs,
    #finvoice2 .customerspecs {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 4px !important;
    }
    #finvoice2 .sellerspecs td,
    #finvoice2 .sellerspecs th,
    #finvoice2 .customerspecs td,
    #finvoice2 .customerspecs th {
        border: 1px solid #dee2e6;
        padding: 6px 3px;
        color: #000 !important;
        font-size: 10px;
        text-align: right !important;
        line-height: 1.1;
    }
    #finvoice2 {
        color: #000 !important;
    }
    #finvoice2 * {
        color: #000 !important;
    }
    #finvoice2 .mb-3 {
        margin-bottom: 4px !important;
    }
    #finvoice2 .border {
        margin-bottom: 2px !important;
        padding: 1px !important;
    }
    #finvoice2 h4 {
        font-size: 12px;
        margin-bottom: 2px !important;
        line-height: 1.2;
    }
    #finvoice2 p {
        margin-bottom: 1px !important;
        font-size: 10px;
        line-height: 1.1;
    }
    #finvoice2 .card-body {
        padding: 4px !important;
    }
    #finvoice2 .d-flex {
        margin-bottom: 2px !important;
    }
    #finvoice2 .factortable {
        margin-bottom: 4px !important;
    }
    #finvoice2 .sellerspecs,
    #finvoice2 .customerspecs {
        margin-bottom: 4px !important;
    }
    #finvoice2 .row {
        margin: 0 !important;
    }
    #finvoice2 .col-sm-12 {
        padding: 0 !important;
    }
    @media print {
        #finvoice2 .factortable td[style*="height"] {
            height: 45px !important;
            padding: 3px !important;
        }
    }
</style>
<div id="finvoice2" class="col-xl-12 box-margin height-card d-none hideprint">
    <div class="card card-body">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="d-flex justify-content-between">
                    <div class="logo"><img src="{{ asset('img/logo/darklogo.png') }}"/></div>
                    <div><h4>صورتحساب فروش کالا </h4></div>
                    <div>
                        <p>شماره فاکتور: <span>{{$request->number}}</span></p>
                        <p>تاریخ: <span>{{\Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at))}}</span></p>
                        <p>کد فاکتور: {{ $request->number }}-B</p>
                    </div>
                </div>
                
                <!-- Seller Information -->
                <div class="d-flex justify-content-center border">
                    <div class="text-dark p-1">مشخصات فروشنده</div>
                </div>
                <table class="table sellerspecs">
                    <tbody>
                    <tr>
                        <td>نام شخص حقیقی / حقوقی : شرکت روغن موتور قم</td>
                        <td>شماره اقتصادی : 411134945318</td>
                        <td>شماره ثبت :</td>
                    </tr>
                    <tr>
                        <td>استان: قم</td>
                        <td>شهرستان : سلفچگان</td>
                        <td>کد پستی ده رقمی : 3746139845</td>
                    </tr>
                    <tr>
                        <td>شناسه ملی : 10860961755</td>
                        <td>تلفن / فکس : 02533673907</td>
                        <td>نشانی : شهرک صنعتی سلفچگان - خ سینا - خیابان فتح</td>
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
                        <th scope="col">نام کالا</th>
                        <th scope="col">تعداد / مقدار</th>
                        <th scope="col">واحد</th>
                        <th scope="col">تعداد کارتن</th>
                        <th scope="col">تعداد در کارتن</th>
                        <th scope="col">فی</th>
                        <th scope="col">جمع کل</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                            // Calculate totals based on net (returned-adjusted) amounts
                            $totalAmountNet = 0;
                            $vatAmount = 0;
                            $totalWithVat = 0;
                        @endphp
                        @foreach($effectiveCommodities as $commodity)
                            @php
                                $displayAmount = $commodity->effective_amount ?? 0;
                                $unitPrice = $commodity->effective_price ?? ($commodity->pivot->price ?? null);
                                $lineTotal = $unitPrice !== null ? $displayAmount * $unitPrice : 0;
                                $totalAmountNet += $lineTotal;
                            @endphp
                            <tr>
                                <td scope="row">{{ $i }}</td>
                                <td>{{ $commodity->title }}</td>
                                <td>{{ number_format($displayAmount, 0, '.', ',') }}</td>
                                <td>{{ $commodity->effective_unit ? $commodity->effective_unit->name : ($commodity->unit->name ?? 'نامشخص') }}</td>
                                <td>
                                    @if(isset($request->box_quantities[$commodity->id]) && $request->box_quantities[$commodity->id]['can_calculate'])
                                        {{ $request->box_quantities[$commodity->id]['boxes'] }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if(isset($request->box_quantities[$commodity->id]) && $request->box_quantities[$commodity->id]['can_calculate'])
                                        {{ $request->box_quantities[$commodity->id]['pieces_per_box'] }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $unitPrice !== null ? number_format($unitPrice, 0) : '-' }}</td>
                                <td>{{ $unitPrice !== null ? number_format($lineTotal, 0) : '-' }}</td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                        @php
                            $vatAmount = $totalAmountNet * vat_rate();
                            $totalWithVat = $totalAmountNet + $vatAmount;
                        @endphp
                        <tr>
                            <td colspan="4" rowspan="5" class="text-left" style="vertical-align: top; padding: 3px !important;">
                                <div class="d-flex justify-content-between" style="margin-bottom: 2px;">
                                    <span>شرایط و نحوه تسویه: </span>
                                    <span>نقدی <span class="border" style="display:inline-block;width:10px;height:10px"></span></span>
                                    <span>غیرنقدی <span class="border" style="display:inline-block;width:10px;height:10px"></span></span>
                                </div>
                                <p style="margin-bottom: 1px;">توضیحات:</p>
                            </td>
                            <td colspan="4" class="text-left">جمع کل : {{ number_format($totalAmountNet, 0) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-left"> مالیات بر ارزش افزوده (%{{ number_format(vat_percentage(), 0) }}) : {{ number_format($vatAmount, 0) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-left">جمع کل با مالیات : {{ number_format($totalWithVat, 0) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-left">جمع کل به حروف: 
                                @php
                                    use NumberToWords\NumberToWords;
                                    $numberToWords = NumberToWords::transformNumber('fa', $totalWithVat);
                                @endphp
                                {{ $numberToWords }} ریال
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right" style="height: 45px; padding: 3px !important;">مهر و امضای فروشنده:</td>
                            <td colspan="4" class="text-right" style="height: 45px; padding: 3px !important;">مهر و امضای خریدار:</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
