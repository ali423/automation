<!-- Invoice Template for {{ $invoiceType }} -->
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

                <div class="mb-2">
                    <div><strong>راننده:</strong> <span>{{ $request->driver_name ?? 'نامشخص' }}</span></div>
                    <div><strong>کد ملی راننده:</strong> <span>{{ $request->driver_national_id ?? $request->driverNationalId ?? '-' }}</span></div>
                    <div><strong>شماره بارنامه:</strong> <span>{{ $request->bill_of_lading_number ?? $request->billOfLadingNumber ?? '-' }}</span></div>
                    <div><strong>تلفن راننده:</strong> <span>{{ $request->driver_phone ?? '-' }}</span></div>
                    <div><strong>وسیله نقلیه:</strong> <span>{{ $request->vehicle_type ?? '-' }}</span></div>
                    <div><strong>پلاک:</strong> 
                        <span>{{ trim(($request->plate_serial ?? '') . ' ' . ($request->plate_number ?? '')) ?: '-' }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>استان:</strong> <span>{{ $request->customer ? $request->customer->province : '' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>شهر:</strong> <span>{{ $request->customer ? $request->customer->city : '' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <table class="table-borderless" style="border: 0.5px solid #e0e0e0;">
                        <colgroup>
                            <col span="1" style="width: 5%;">
                            <col span="1" style="width: 35%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 10%;">
                            @if($invoiceType === 'documentation')
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
                                <th scope="col">تعداد بسته‌بندی (کارتن)</th>
                                @if($invoiceType === 'documentation')
                                    <th scope="col">فی(ریال)</th>
                                    <th scope="col">جمع(ریال)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                                // Pre-calculate total price once to avoid multiple attribute calls
                                $totalPrice = $request->total_price ?? null;
                            @endphp
                            @foreach($request->commodities as $commodity)
                                <tr>
                                    <td scope="row">{{ $i }}</td>
                                    <td style="text-align: center;">{{ $commodity->title }}</td>
                                    <td>{{ $commodity->pivot->unit ? $commodity->pivot->unit->name : 'نامشخص' }}</td>
                                    <td>{{ number_format($commodity->pivot->amount, 0, '.', '') }}</td>
                                    <td>
                                        @php
                                            $piecesPerBox = $commodity->pieces_per_box ?? 1;
                                            if ($piecesPerBox > 0) {
                                                // Convert to main unit (pieces) first, then divide by pieces per box
                                                $commodityUnitService = app(\App\Services\CommodityUnitService::class);
                                                $amountInMainUnit = $commodityUnitService->convertToMainUnit($commodity, $commodity->pivot->amount, $commodity->pivot->unit_id);
                                                // Packaging Quantity = Quantity (in pieces) ÷ Quantity per Package (pieces_per_box)
                                                $packagingQuantity = $amountInMainUnit !== null ? floor($amountInMainUnit / $piecesPerBox) : '-';
                                            } else {
                                                $packagingQuantity = '-';
                                            }
                                        @endphp
                                        {{ $packagingQuantity !== '-' ? number_format($packagingQuantity, 0, '.', '') : '-' }}
                                    </td>
                                    @if($invoiceType === 'documentation')
                                        <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->price) : '-' }}</td>
                                        <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->amount * $commodity->pivot->price) : '-' }}</td>
                                    @endif
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                            <tr>
                                <td colspan="{{ $invoiceType === 'documentation' ? '7' : '5' }}" class="text-right">
                                    مجموع مقدار: {{ number_format($request->commodities->sum('pivot.amount'), 0, '.', '') }}
                                    @if($invoiceType === 'documentation')
                                        <br>مجموع: {{ isset($totalPrice) && isset($totalPrice['number']) ? number_format($totalPrice['number']) : '0' }}
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
