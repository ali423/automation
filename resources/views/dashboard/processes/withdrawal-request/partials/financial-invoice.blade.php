<!-- Financial Invoice Template -->
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
                        <td class="text-left">نام شخص حقیقی / حقوقی : شرکت روغن موتور قم<span> </span></td>
                        <td></td>
                        <td></td>
                        <td>شماره اقتصادی : 411134945318</td>
                        <td></td>
                        <td>شماره ثبت :</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-left">استان: <span>قم</span></td>
                        <td>شهرستان : سلفچگان</td>
                        <td></td>
                        <td>کد پستی ده رقمی : 3746139845</td>
                        <td></td>
                        <td>شناسه ملی : 10860961755</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-left">نشانی : <span>شهرک صنعتی سلفچگان - خ سینا - خیابان فتح</span></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>تلفن / فکس : 02533673907</td>
                        <td></td>
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
                        <td></td>
                        <td></td>
                        <td>شماره اقتصادی: {{$request->customer ? ($request->customer->economic_code ?? '') : ''}}</td>
                        <td></td>
                        <td> شماره ملی:{{ $request->customer ? ($request->customer->national_code ?? '') : ''}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-left">استان: <span>{{ $request->customer ? ($request->customer->province ?? '') : '' }}</span></td>
                        <td>شهرستان: {{ $request->customer ? ($request->customer->city ?? '') : '' }}</td>
                        <td></td>
                        <td> کدپستی:{{$request->customer ? ($request->customer->zip_code ?? '') : ''}}</td>
                        <td></td>
                        <td>شهر: {{ $request->customer ? ($request->customer->city ?? '') : '' }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-left">آدرس: <span>{{$request->customer ? ($request->customer->address ?? '') : ''}} </span></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>تلفن: {{$request->customer ? ($request->customer->mobile ?? '') : ''}}</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                
                <!-- Items Table -->
                <table class="factortable table table-bordered text-center">
                    <thead>
                    <tr class="table-secondary">
                        <th scope="col">ردیف</th>
                        <th scope="col">کد کالا</th>
                        <th scope="col">نام کالا</th>
                        <th scope="col">تعداد / مقدار</th>
                        <th scope="col">واحد</th>
                        <th scope="col">تعداد کارتن</th>
                        <th scope="col">تعداد در کارتن</th>
                        <th scope="col">تعداد اضافی</th>
                        <th scope="col">فی</th>
                        <th scope="col">جمع کل</th>
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
                                <td>{{ $commodity->number }}</td>
                                <td>{{ $commodity->title }}</td>
                                <td>{{ $commodity->pivot->amount }}</td>
                                <td>{{ $commodity->pivot->unit ? $commodity->pivot->unit->name : 'نامشخص' }}</td>
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
                                <td>
                                    @if(isset($request->box_quantities[$commodity->id]) && $request->box_quantities[$commodity->id]['can_calculate'])
                                        {{ $request->box_quantities[$commodity->id]['remaining_pieces'] }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->price) : '-' }}</td>
                                <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->amount * $commodity->pivot->price) : '-' }}</td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                        <tr>
                            <td colspan="10" rowspan="4" class="text-left" style="vertical-align: top">
                                <div class="d-flex justify-content-between">
                                    <span>شرایط و نحوه تسویه: </span>
                                    <span>نقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                    <span>غیرنقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                </div>
                                <p>توضیحات:</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-left"> مالیات بر ارزش افزوده : %{{ number_format(vat_percentage(), 0) }} </td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-left">جمع کل : {{ isset($totalPrice) && isset($totalPrice['number']) ? number_format($totalPrice['number']) : '0' }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-left">جمع کل به حروف: 
                                @if(isset($totalPrice) && isset($totalPrice['world']))
                                    {{ $totalPrice['world'] }} ریال
                                @else
                                    صفر ریال
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" class="text-left" style="height: 120px">مهر و امضای فروشنده:</td>
                            <td colspan="9" class="text-left">مهر و امضای خریدار:</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
