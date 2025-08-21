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
                            <col span="1" style="width: 8%;">
                            <col span="1" style="width: 40%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 10%;">
                            @if($invoiceType === 'documentation')
                                <col span="1" style="width: 13%;">
                                <col span="1" style="width: 14%;">
                            @endif
                        </colgroup>
                        <thead>
                            <tr class="table-header">
                                <th scope="col">ردیف</th>
                                <th scope="col">برند</th>
                                <th scope="col">مدل</th>
                                <th scope="col">واحد</th>
                                <th scope="col">تعداد</th>
                                @if($invoiceType === 'documentation')
                                    <th scope="col">فی(ریال)</th>
                                    <th scope="col">جمع(ریال)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php($i = 1)
                            @foreach($request->commodities as $commodity)
                                <tr>
                                    <td scope="row">{{ $i }}</td>
                                    <td>{{ $commodity->brand ?? 'زیگما' }}</td>
                                    <td style="text-align: center;">{{ $commodity->title }}</td>
                                    <td>{{ $commodity->pivot->unit_id ? (($unit = \App\Models\Unit::find($commodity->pivot->unit_id)) ? $unit->name : 'نامشخص') : 'نامشخص' }}</td>
                                    <td>{{ $commodity->pivot->amount }}</td>
                                    @if($invoiceType === 'documentation')
                                        <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->price) : '-' }}</td>
                                        <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->amount * $commodity->pivot->price) : '-' }}</td>
                                    @endif
                                </tr>
                                @php($i++)
                            @endforeach
                            <tr>
                                <td colspan="{{ $invoiceType === 'documentation' ? '7' : '5' }}" class="text-right">
                                    مجموع وزن / مقدار: {{ $request->commodities->sum('pivot.amount') }}
                                    @if($invoiceType === 'documentation')
                                        <br>مجموع: {{ isset($request->total_price) && isset($request->total_price['number']) ? number_format($request->total_price['number']) : '0' }}
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mb-5">
                    اینجانب <span style="display:inline-block;width: 100px;border-bottom:1px dashed #000"> </span>
                    راننده خودرو به شماره پلاک 
                    <div class="pelak"> </div>
                    <div class="pelak" style="width: 100px"> </div>
                    شماره تماس <span style="display:inline-block;width: 100px;border-bottom:1px dashed #000"> </span>
                    محموله فوق را تحویل گرفتم.
                </div>
                
                <div class="d-flex justify-content-around align-items-center mb-3">
                    <h6>امضاء تحویل گیرنده کالا</h6>
                    <h6>امضاء متصدی شرکت</h6>
                </div>
            </div>
        </div>
    </div>
</div>
