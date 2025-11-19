<!-- Tejarat Invoice Template -->
<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
        #finvoice-tejarat {
            overflow: visible !important;
            height: auto !important;
            max-height: none !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            page-break-after: avoid !important;
        }
        #finvoice-tejarat .card {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
            page-break-inside: avoid !important;
        }
        #finvoice-tejarat .card-body {
            padding: 5px !important;
            margin: 0 !important;
        }
        #finvoice-tejarat .row {
            margin: 0 !important;
        }
        #finvoice-tejarat .col-sm-12,
        #finvoice-tejarat .col-xs-12 {
            padding: 0 !important;
        }
        .factortable {
            border: 1px solid #000 !important;
            border-collapse: collapse !important;
            width: 100% !important;
            margin: 0 !important;
            page-break-inside: avoid !important;
            font-size: 10px !important;
        }
        .factortable th,
        .factortable td {
            border: 1px solid #000 !important;
            padding: 4px 3px !important;
            font-size: 10px !important;
            line-height: 1.2 !important;
        }
        .factortable thead {
            display: table-header-group !important;
        }
        .factortable thead tr {
            page-break-after: avoid !important;
        }
        .factortable tbody {
            display: table-row-group !important;
        }
        .factortable tbody tr {
            page-break-inside: avoid !important;
        }
        .factortable tbody tr:last-child {
            page-break-after: avoid !important;
        }
        .sellerspecs,
        .customerspecs {
            border-collapse: collapse !important;
            width: 100% !important;
            margin: 5px 0 !important;
            font-size: 10px !important;
            border: 1px solid #000 !important;
        }
        .sellerspecs td,
        .sellerspecs th,
        .customerspecs td,
        .customerspecs th {
            border: 1px solid #000 !important;
            padding: 4px !important;
            font-size: 10px !important;
        }
        .logo img {
            max-width: 120px !important;
            height: auto !important;
        }
        h4 {
            font-size: 14px !important;
            margin: 5px 0 !important;
        }
        p {
            margin: 2px 0 !important;
            font-size: 10px !important;
        }
    }
    @media screen {
        #finvoice-tejarat.showprint {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            max-height: 100vh !important;
            height: auto !important;
        }
        #finvoice-tejarat.showprint .card {
            max-width: 100% !important;
            margin: 0 auto !important;
        }
        .factortable {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .factortable th,
        .factortable td {
            border: 1px solid #dee2e6 !important;
            padding: 8px 4px !important;
        }
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
                
                <!-- Seller Information -->
                <div class="d-flex justify-content-center border">
                    <div class="text-dark p-1">مشخصات فروشنده</div>
                </div>
                <table class="table sellerspecs">
                    <tbody>
                    <tr>
                        <td class="text-left">نام شخص حقیقی / حقوقی : شرکت روغن موتور قم<span> </span></td>
                        <td>شماره اقتصادی : 411134945318</td>
                        <td>شماره ثبت :</td>
                    </tr>
                    <tr>
                        <td class="text-left">استان: <span>قم</span></td>
                        <td>شهرستان : سلفچگان</td>
                        <td> کد پستی ده رقمی : 3746139845</td>
                        <td>شناسه ملی : 10860961755</td>
                    </tr>
                    <tr>
                        <td class="text-left">نشانی : <span>شهرک صنعتی سلفچگان - خ سینا - خیابان فتح</span></td>
                        <td>تلفن / فکس : 02533673907</td>
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
                        <td class="text-left">
                             نام خریدار: <span>{{ $request->customer ? $request->customer->name.'-'. ($request->customer->comp_name ?? '') : 'نامشخص' }} </span></td>
                        <td>شماره اقتصادی: {{$request->customer ? ($request->customer->economic_code ?? '') : ''}}</td>
                        <td> شماره ملی:{{ $request->customer ? ($request->customer->national_code ?? '') : ''}}</td>
                    </tr>
                    <tr>
                        <td class="text-left">استان: <span>{{ $request->customer ? ($request->customer->province ?? '') : '' }}</span></td>
                        <td>شهرستان: {{ $request->customer ? ($request->customer->city ?? '') : '' }}</td>
                        <td> کدپستی:{{$request->customer ? ($request->customer->zip_code ?? '') : ''}}</td>
                        <td>شهر: {{ $request->customer ? ($request->customer->city ?? '') : '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-left">آدرس: <span>{{$request->customer ? ($request->customer->address ?? '') : ''}} </span></td>
                        <td>تلفن: {{$request->customer ? ($request->customer->mobile ?? '') : ''}}</td>
                    </tr>
                    </tbody>
                </table>
                
                <!-- Items Table -->
                <table class="factortable table text-center">
                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 8%;">
                        <col style="width: 10%;">
                        <col style="width: 20%;">
                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 11%;">
                        <col style="width: 10%;">
                    </colgroup>
                    <thead>
                    <tr class="table-secondary">
                        <th scope="col">ردیف</th>
                        <th scope="col">کد کالا</th>
                        <th scope="col">شناسه کالا</th>
                        <th scope="col">نام کالا</th>
                        <th scope="col">تعداد / مقدار</th>
                        <th scope="col">واحد</th>
                        <th scope="col">حجم (لیتر)</th>
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
                                // Calculate litrage: Convert amount to base unit, then multiply by litrage per base unit
                                $amountInBaseUnit = $commodityUnitService->convertToMainUnit(
                                    $commodity,
                                    $commodity->pivot->amount,
                                    $commodity->pivot->unit_id
                                );
                                $totalLitrage = ($amountInBaseUnit ?? 0) * ($commodity->litrage ?? 0);
                            @endphp
                            <tr>
                                <td scope="row">{{ $i }}</td>
                                <td>{{ $commodity->number }}</td>
                                <td>{{ $commodity->product_identifier ?? default_product_identifier() }}</td>
                                <td>{{ $commodity->title }}</td>
                                <td>{{ number_format($commodity->pivot->amount) }}</td>
                                <td>{{ $commodity->pivot->unit ? $commodity->pivot->unit->name : 'نامشخص' }}</td>
                                <td>{{ number_format($totalLitrage, 2) }}</td>
                                <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->price) : '-' }}</td>
                                <td>{{ number_format(vat_percentage(), 0) }}%</td>
                                <td>{{ isset($commodity->pivot->price) ? number_format(round($commodity->pivot->amount * $commodity->pivot->price * (1 + vat_rate()))) : '-' }}</td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                        <tr>
                        <td colspan="7" rowspan="3" class="text-left" style="vertical-align: top">
                            <div class="d-flex justify-content-between">
                                <span>شرایط و نحوه تسویه: </span>
                                <span>نقدی <span class="border"
                                                 style="display:inline-block;width:15px;height:15px"></span></span>
                                <span>غیرنقدی <span class="border"
                                                    style="display:inline-block;width:15px;height:15px"></span></span>
                            </div>
                            <p>توضیحات:</p>
                        </td>
                        <td colspan="3" class="text-left">جمع کل : 
                            @if(isset($totalPrice) && isset($totalPrice['number']))
                                {{ number_format(round($totalPrice['number'] * 1.1)) }}
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-left">جمع کل به حروف:
                            @if(isset($totalPrice) && isset($totalPrice['world']))
                                {{ $totalPrice['world'] }} ریال
                            @else
                                صفر ریال
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="height: 20px;"></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-left" style="height: 120px">مهر و امضای فروشنده:</td>
                        <td colspan="5" class="text-left">مهر و امضای خریدار:</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
