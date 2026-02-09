<!-- Invoice Template for {{ $invoiceType }} -->
<style>
    @media print {
        @page {
            size: landscape;
        }
        #invoice-{{ $invoiceType }} {
            font-size: 9px !important;
        }
        #invoice-{{ $invoiceType }} .factortable {
            border: 2px solid #000 !important;
        }
        #invoice-{{ $invoiceType }} .factortable th,
        #invoice-{{ $invoiceType }} .factortable td {
            border: 2px solid #000 !important;
            font-size: 9px !important;
            padding: 4px !important;
            color: #000 !important;
        }
    }
    @media screen {
        #invoice-{{ $invoiceType }} {
            font-size: 10px;
        }
        #invoice-{{ $invoiceType }} .factortable th,
        #invoice-{{ $invoiceType }} .factortable td {
            font-size: 10px;
            color: #000 !important;
        }
    }
    #invoice-{{ $invoiceType }} .factortable {
        border-collapse: collapse;
        width: 100%;
        direction: rtl;
    }
    #invoice-{{ $invoiceType }} .factortable th,
    #invoice-{{ $invoiceType }} .factortable td {
        text-align: center !important;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 8px 4px;
        color: #000 !important;
    }
    #invoice-{{ $invoiceType }} .factortable thead tr {
        background-color: #f8f9fa;
    }
    #invoice-{{ $invoiceType }} .shipping-info {
        direction: rtl;
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 0 !important;
    }
    @media print {
        #invoice-{{ $invoiceType }} .shipping-info {
            border: 2px solid #000 !important;
        }
        #invoice-{{ $invoiceType }} .shipping-info td {
            border: 2px solid #000 !important;
            font-size: 9px !important;
            padding: 4px !important;
            color: #000 !important;
        }
    }
    @media screen {
        #invoice-{{ $invoiceType }} .shipping-info td {
            font-size: 10px;
            border: 1px solid #dee2e6;
            color: #000 !important;
        }
    }
    #invoice-{{ $invoiceType }} {
        color: #000 !important;
    }
    #invoice-{{ $invoiceType }} * {
        color: #000 !important;
    }
</style>
<div id="invoice-{{ $invoiceType }}" class="invoice col-xl-12 box-margin height-card d-none">
    <div class="card card-body">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <img src="{{ asset('img/logo/darklogo.png') }}" class="logo" />
                    <div class="text-center">
                        <h4>خروج کالا از انبار</h4>
                        <div class="factor {{ $invoiceType }}">
                            @if($invoiceType === 'customer')
                                ( نسخه مشتری )
                            @elseif($invoiceType === 'documentation')
                                ( نسخه حسابداری )
                            @elseif($invoiceType === 'warehouse')
                                ( نسخه بارگیری )
                            @endif
                        </div>
                    </div>
                    <div>تاریخ: <span>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}</span></div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>خریدار/ نماینده خریدار: <span>{{ $request->customer ? $request->customer->name : 'نامشخص' }}</span></div>
                    <div>شماره درخواست: <span>{{ $request->number }}</span></div>
                </div>

                <div class="mb-1">
                    <table class="table table-sm shipping-info factortable" style="margin-bottom: 0;">
                        <tbody>
                            <tr>
                                <td><strong>راننده:</strong> <span>{{ $request->driver_name ?? 'نامشخص' }}</span></td>
                                <td><strong>کد ملی راننده:</strong> <span>{{ $request->driver_national_id ?? '-' }}</span></td>
                                <td><strong>شماره بارنامه:</strong> <span>{{ $request->bill_of_lading_number ?? '-' }}</span></td>
                                <td><strong>تلفن راننده:</strong> <span>{{ $request->driver_phone ?? '-' }}</span></td>
                                <td><strong>وسیله نقلیه:</strong> <span>{{ $request->vehicle_type ?? '-' }}</span></td>
                                <td><strong>پلاک:</strong> <span>{{ trim(($request->plate_serial ?? '') . ' ' . ($request->plate_number ?? '')) ?: '-' }}</span></td>
                                <td><strong>استان:</strong> <span>{{ $request->shipping_province ?? ($request->customer ? ($request->customer->province ?? '') : '-') }}</span></td>
                                <td><strong>شهر:</strong> <span>{{ $request->shipping_city ?? ($request->customer ? ($request->customer->city ?? '') : '-') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mb-1">
                    <table class="factortable table table-bordered">
                        <colgroup>
                            <col span="1" style="width: 5%;">
                            <col span="1" style="width: 30%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 10%;">
                            @if($invoiceType !== 'documentation')
                                <col span="1" style="width: 10%;">
                                <col span="1" style="width: 10%;">
                            @endif
                            @if($invoiceType === 'documentation')
                                <col span="1" style="width: 10%;">
                                <col span="1" style="width: 10%;">
                                <col span="1" style="width: 10%;">
                            @endif
                        </colgroup>
                        <thead>
                            <tr class="table-header">
                                <th scope="col">ردیف</th>
                                <th scope="col">مدل</th>
                                <th scope="col">واحد</th>
                                <th scope="col">تعداد</th>
                                @if($invoiceType !== 'documentation')
                                    <th scope="col">تعداد بسته‌بندی (کارتن)</th>
                                    <th scope="col">وزن (کیلوگرم)</th>
                                @endif
                                @if($invoiceType === 'documentation')
                                    <th scope="col">فی(ریال)</th>
                                    <th scope="col">جمع(ریال)</th>
                                    <th scope="col">ارزش افزوده (ریال)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                                // Pre-calculate total price once to avoid multiple attribute calls
                                $totalPrice = $request->total_price ?? null;
                                $commodityUnitService = app(\App\Services\CommodityUnitService::class);
                                $totalPackaging = 0;
                                $totalWeight = 0;
                                $hasValidWeight = false;
                                // Calculate VAT totals for documentation invoice using net amounts
                                $totalAmountNet = 0;
                                $totalVatAmount = 0;
                                $totalWithVat = 0;
                            @endphp
                            @foreach($request->commodities as $commodity)
                                @php
                                    $displayAmount = isset($commodity->net_amount) ? $commodity->net_amount : $commodity->pivot->amount;
                                    // Calculate packaging quantity only for non-documentation invoices
                                    $packagingQuantity = '-';
                                    $weight = null;
                                    if ($invoiceType !== 'documentation') {
                                        $piecesPerBox = $commodity->pieces_per_box ?? 1;
                                        if ($piecesPerBox > 0) {
                                            // Convert to main unit (pieces) first, then divide by pieces per box
                                            $amountInMainUnit = $commodityUnitService->convertToMainUnit($commodity, $displayAmount, $commodity->pivot->unit_id);
                                            // Packaging Quantity = Quantity (in pieces) ÷ Quantity per Package (pieces_per_box)
                                            $packagingQuantity = $amountInMainUnit !== null ? floor($amountInMainUnit / $piecesPerBox) : '-';
                                            if ($packagingQuantity !== '-') {
                                                $totalPackaging += $packagingQuantity;
                                            }
                                        }
                                        // Calculate weight for customer and warehouse invoices
                                        $weight = calculate_weight($commodity, $displayAmount, $commodity->pivot->unit_id);
                                        if ($weight !== null) {
                                            $totalWeight += $weight;
                                            $hasValidWeight = true;
                                        }
                                    }
                                    // Calculate VAT amount for documentation invoice
                                    $vatAmount = 0;
                                    if ($invoiceType === 'documentation' && isset($commodity->pivot->price)) {
                                        $lineTotal = $displayAmount * $commodity->pivot->price;
                                        $vatAmount = $lineTotal * vat_rate();
                                        $totalAmountNet += $lineTotal;
                                        $totalVatAmount += $vatAmount;
                                    }
                                @endphp
                                <tr>
                                    <td scope="row">{{ $i }}</td>
                                    <td>{{ $commodity->title }}</td>
                                    <td>{{ $commodity->pivot->unit ? $commodity->pivot->unit->name : 'نامشخص' }}</td>
                                    <td>{{ number_format($displayAmount, 0, '.', ',') }}</td>
                                    @if($invoiceType !== 'documentation')
                                        <td>
                                            {{ $packagingQuantity !== '-' ? number_format($packagingQuantity, 0, '.', ',') : '-' }}
                                        </td>
                                        <td>
                                            {{ $weight !== null ? number_format($weight, 0, '.', ',') : 'نامشخص' }}
                                        </td>
                                    @endif
                                    @if($invoiceType === 'documentation')
                                        <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->price, 0) : '-' }}</td>
                                        <td>{{ isset($commodity->pivot->price) ? number_format($displayAmount * $commodity->pivot->price, 0) : '-' }}</td>
                                        <td>{{ isset($commodity->pivot->price) ? number_format($vatAmount, 0, '.', ',') : '0' }}</td>
                                    @endif
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                            <tr>
                                <td colspan="{{ $invoiceType === 'documentation' ? '7' : '6' }}">
                                    @if($invoiceType !== 'documentation')
                                        کل بسته‌بندی: {{ number_format($totalPackaging, 0, '.', ',') }} | 
                                        وزن کل: {{ $hasValidWeight ? number_format($totalWeight, 0, '.', ',') . ' کیلوگرم' : 'نامشخص' }}
                                    @endif
                                    @if($invoiceType === 'documentation')
                                        @php
                                            $totalWithVat = $totalAmountNet + $totalVatAmount;
                                        @endphp
                                        مجموع: {{ number_format($totalAmountNet, 0) }} | 
                                        مالیات بر ارزش افزوده (%{{ number_format(vat_percentage(), 0) }}): {{ number_format($totalVatAmount, 0) }} | 
                                        جمع کل با مالیات: {{ number_format($totalWithVat, 0) }}
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mb-4 mt-3">
                    اینجانب <span style="display:inline-block;width: 120px;border-bottom:1px dashed #000"></span>
                    به عنوان راننده/نماینده خریدار، محموله فوق را
                    به صورت کامل و صحیح و سالم تحویل گرفتم.
                </div>

                <div class="d-flex justify-content-around align-items-center mb-3">
                    <h6>امضاء و اثر انگشت تحویل گیرنده کالا</h6>
                    <h6>امضاء متصدی/مسئول انبار</h6>
                </div>
            </div>
        </div>
    </div>
</div>
