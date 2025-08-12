@extends('layouts.main')
@section('title', 'جزئیات درخواست فروش کالا')

@section('page_styles')
<link rel="stylesheet" href="{{ asset('css/imexport-print.css') }}">
<link rel="stylesheet" href="{{ asset('css/imfactor-print.css') }}">
@endsection

@section('content')
    @php
        $receiptType = request('receipt_type', 'customer');
    @endphp
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزئیات درخواست فروش کالا</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.status') }}</label>
                                <input type="text" name="status"
                                       value="{{ $request->status ? __('fields.withdrawal-request.status')[$request->status] : '-' }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.status') }}"
                                       autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.customer') }}</label>
                                <input type="text" name="status"
                                       value="{{ $request->customer ? $request->customer->name : 'نامشخص' }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.customer') }}"
                                       autocomplete="off" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.created_at') }}</label>
                                <input type="text" name="name"
                                       value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.creator') }}</label>
                                <input type="text" name="name"
                                       @if (isset($request->creator_user) && $request->creator_user)
                                           value="{{ $request->creator_user->full_name }}"
                                       @else
                                           value="سیستم"
                                       @endif
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.importing_request.number') }}</label>
                                <input type="text" name="status"
                                       value="{{ $request->number}}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.importing_request.number') }}"
                                       autocomplete="off" disabled>
                            </div>
                        </div>
                        @php
                            $i = 1;
                            $total_amount = 0;
                        @endphp
                        @foreach ($request->commodities as $commodity)
                            <div id="inputFormRow" class="form-row shadow p-4 m-3">
                                <div class="showbarrel">
                                    <i class="fa fa-database"></i>
                                    <div>
                                        <span>Main Unit Amount</span>
                                        @php
                                            $mainUnitData = $request->getMainUnitAmountAttribute();
                                            $commodityMainUnit = $mainUnitData[$commodity->id] ?? null;
                                        @endphp
                                        <span>
                                            @if($commodityMainUnit && isset($commodityMainUnit['main_unit_amount']))
                                                {{ number_format($commodityMainUnit['main_unit_amount'], 2) }} {{ $commodityMainUnit['main_unit_name'] . ' (' . $commodityMainUnit['main_unit_symbol'] . ')' }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
                                    <select id="commodity_id" class="form-control" name="commodity_id[0]" disabled>
                                        <option value="{{ $commodity->id }}">{{ $commodity->title }}</option>
                                    </select>
                                    <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="unit"> {{ __('fields.unit') }}</label>
                                    <input type="text"
                                        value="{{ $commodity->pivot->unit_id ? (\App\Models\Unit::find($commodity->pivot->unit_id)->name . ' (' . \App\Models\Unit::find($commodity->pivot->unit_id)->symbol . ')') : '-' }}"
                                        id="unit" name="unit" class="form-control" disabled>
                                    <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="amount"> {{ __('fields.commodity.amount') }}</label>
                                    <input type="number" value="{{ $commodity->pivot->amount }}" id="amount"
                                        min="1" name="amount[0]" class="form-control" autocomplete="off"
                                        placeholder="{{ __('fields.commodity.amount') }}" pattern="[0-9 .]" disabled>
                                    <div class="invalid-feedback">
                                        لطفاً {{ __('fields.commodity.amount') }} را وارد کنید.
                                    </div>
                                </div>
                                @if(isset($commodity->pivot->price))
                                    <div class="form-group col-md-3">
                                        <label for="price"> {{  __('fields.sell-price_per_unit') }}</label>
                                        <input type="text" id="price" placeholder="{{ number_format($commodity->pivot->price) }}" class="form-control" disabled>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @foreach ($request->comments as $comment)
                            <div class="form-group mb-20">
                                <label for="comment"> {{ $comment->user ? $comment->user->full_name : 'کاربر نامشخص' }} در تاریخ :
                                    {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($comment->created_at)) }}</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment"
                                          rows="6" disabled>{{ $comment->body }}</textarea>
                            </div>
                        @endforeach
                        <div class="col-xl-12 height-card box-margin">
                            <div class="card">
                                <div class="card-body">
                                    <div class="bg-transparent d-flex align-items-center justify-content-between">
                                        <div class="widgets-card-title">
                                            <h5 class="card-title">فایل ضمیمه شده</h5>
                                        </div>
                                    </div>
                                @foreach ($request->files as $file)
                                    <!-- Single Download File -->
                                        <div
                                            class="widget-download-file d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center mr-3">
                                                <div class="download-file-icon mr-3">
                                                    <img src="{{ asset('img/filemanager-img/1.png') }}" alt="">
                                                </div>
                                                <div class="user-text-table">
                                                    <h6 class="d-inline-block font-15 mb-0">{{ $file->name }}</h6>
                                                    <p class="mb-0"> {{ $file->user ? $file->user->full_name : 'کاربر نامشخص' }} در تاریخ :
                                                        {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($file->created_at)) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ asset(str_replace('public', 'storage', $file->source)) }}"
                                               download="proposed_file_name"
                                               class="download-link badge badge-primary badge-pill p-2 font-16"><i
                                                    class="ti-download"></i></a>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                        @if (($request->status == 'approvaled'))
                            <div class="col-xl-12 height-card box-margin">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="bg-transparent d-flex align-items-center justify-content-between">
                                            <div class="widgets-card-title">
                                                <h5 class="card-title"> رسید کالای خروجی</h5>
                                            </div>
                                        </div>
                                        <div class="d-md-flex justify-content-center">
                                            <a href="#" class="factor customerbtn btn btn-secondary m-1"><i
                                                    class="ti-printer font-18"></i> حواله مشتری</a>
                                            <a href="#" class="factor documentationbtn btn btn-secondary m-1"><i class="ti-printer font-18"></i> حواله حسابداری</a>
                                            <a href="#" class="factor warehousebtn btn btn-secondary m-1"><i
                                                    class="ti-printer font-18"></i> حواله بارگیری</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 height-card box-margin">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="bg-transparent d-flex align-items-center justify-content-between">
                                            <div class="widgets-card-title">
                                                <h5 class="card-title">چاپ فاکتور</h5>
                                            </div>
                                        </div>
                                        <div class="d-md-flex justify-content-center">
                                            <a href="#" class="factor factorbtn btn btn-secondary m-1"><i
                                                    class="ti-printer font-18"></i> چاپ فاکتور</a>
                                            <a href="#" class="factor factorbtn2 btn btn-secondary m-1"><i
                                                     class="ti-printer font-18"></i> چاپ فاکتور</a>
                                            <a href="#" class="factor tejaratbtn btn btn-secondary m-1"><i class="ti-printer font-18"></i> چاپ نسخه سامانه تجارت</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6 mb-1 mb-md-0">
                                @if ($request->status == 'awaiting_approval')
                                    <a href="{{ route('approval.withdrawal', $request) }}"
                                       class="btn btn-primary px-1">تایید درخواست</a>
                                @endif
                            </div>
                            <div class="col-md-6 text-md-right">
                                @if ($request->status == 'awaiting_approval')
                                    <a href="{{ route('reject.withdrawal', $request) }}" class="btn btn-danger px-1">رد
                                        درخواست</a>
                                @endif
                                <a href="{{ route('activity.index', [
                                    'object_id' => $request->id,
                                    'object_type' => class_basename($request),
                                ]) }}"
                                   class="btn btn-dfprimary px-1 px-md-4 m-md-0">تاریخچه تغییرات</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- <div id="invoice" class="col-xl-12 box-margin height-card showprint">
            <div class="card card-body">
                {{-- <h4 class="card-title"></h4> --}}
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <img src="{{ asset('img/logo/darklogo.png') }}" class="logo" />
                            <div class="text-center">
                                <h4>
                                    خروج کالا از انبار
                                </h4>
                                <div class="d-none factor customer">( نسخه مشتری )</div>
                                <div class="d-none factor documentation">( نسخه حسابداری )</div>
                                <div class="d-none factor warehouse">( نسخه بارگیری )</div>
                            </div>
                            <div>تاریخ: <span>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}</span></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>خریدار/ نماینده خریدار: <span>{{ $request->customer->name }}</span></div>
                            <div>شماره درخواست: <span>{{$request->number}}</span></div>
                        </div>
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">

                            <table style="border: none;">
                            <tr style="border: none;">
                                <td style="border: none;"></td>
                                    <td class="text-left" style="border: none; font-weight: bold;">استان: <span> </span></td>
                                    <td style="border: none;"></td>
                                    <td style="border: none; font-weight: bold;">شهر:</td>
                                    <td style="border: none;"></td>
                                    <td style="border: none;"></td>
                                    <td style="border: none;"></td>
                            </tr>    
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                        
                            <table class="table-borderless" style="border: 0.5px solid #e0e0e0;">
                                <colgroup>
                                    <col span="1" style="width: 5%;">
                                    <col span="1" style="width: 8%;">
                                    <col span="1" style="width: 40%;">
                                    <col span="1" style="width: 10%;">
                                    <col span="1" style="width: 10%;">
                                    @if($receiptType == 'documentation')
                                        <col span="1" style="width: 13%;">
                                        <col span="1" style="width: 14%;">
                                    @endif
                                </colgroup>
                                <tr class="table-header">
                                    <th scope="col">ردیف</th>
                                    <th scope="col">کالای ورودی</th>
                                    <th scope="col">انبار</th>
                                    <th scope="col">تعداد / مقدار</th>
                                    <th scope="col">توضیحات</th>
                                </tr>
                                @php($i=1)
                                @foreach($request->commodities as $commodity)
                                <tr>
                                    <th scope="row">{{$i}}</th>
                                    <td>{{$commodity->title}}</td>
    <td>-</td>
                                        <td>{{ $commodity->pivot->amount }} {{ $commodity->pivot->unit_id ? (($unit = \App\Models\Unit::find($commodity->pivot->unit_id)) ? $unit->name . ' (' . $unit->symbol . ')' : '-') : '-' }}</td>
                                    <td></td>
                                </tr>
                                        @php($i++)
                                @endforeach
                            </table>
                        </div>
                        <div class="mb-5">
                            اینجانب <span style="display:inline-block;width: 100px;border-bottom:1px dashed #000">&nbsp;</span>
                            راننده خودرو به شماره پلاک 
                            <div class="pelak">&nbsp;&nbsp;</div>
                            <div class="pelak" style="width: 100px">&nbsp;</div>
                            شماره تماس <span style="display:inline-block;width: 100px;border-bottom:1px dashed #000">&nbsp;</span>
                            محموله فوق را تحویل گرفتم.
                        </div>
                        <div class="d-flex justify-content-around align-items-center mb-3">
                            <h6>امضاء تحویل گیرنده کالا</h6>
                            <h6>امضاء متصدی شرکت</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- <div id="invoice-customer" class="invoice col-xl-12 box-margin height-card showprint"> -->
        <div id="invoice-customer" class="invoice col-xl-12 box-margin height-card showprint d-none" >
            <div class="card card-body">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <img src="{{ asset('img/logo/darklogo.png') }}" class="logo" />
                            <div class="text-center">
                                <h4>خروج کالا از انبار</h4>
                                <div class="factor customer">( نسخه مشتری )</div>
                            </div>
                            <div>تاریخ: <span>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}</span></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>خریدار/ نماینده خریدار: <span>{{ $request->customer ? $request->customer->name : 'نامشخص' }}</span></div>
                            <div>شماره درخواست: <span>{{ $request->number }}</span></div>
                        </div>
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                            <table style="border: none;">
                                <tr style="border: none;">
                                    <td style="border: none;"></td>
                                    <td class="text-left" style="border: none; font-weight: bold;">استان: <span>{{ $request->customer ? $request->customer->province : '' }}</span></td>
                                    <td style="border: none;"></td>
                                    <td style="border: none; font-weight: bold;">شهر: <span>{{ $request->customer ? $request->customer->city : '' }}</span></td>
                                    <td style="border: none;"></td>
                                    <td style="border: none;"></td>
                                    <td style="border: none;"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <table class="table-borderless" style="border: 0.5px solid #e0e0e0;">
                                <colgroup>
                                    <col span="1" style="width: 5%;">
                                    <col span="1" style="width: 8%;">
                                    <col span="1" style="width: 40%;">
                                    <col span="1" style="width: 10%;">
                                    <col span="1" style="width: 10%;">
                                </colgroup>
                                <thead>
                                    <tr class="table-header">
                                        <th scope="col">ردیف</th>
                                        <th scope="col">برند</th>
                                        <th scope="col">مدل</th>
                                        <th scope="col">واحد</th>
                                        <th scope="col">تعداد</th>
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
                                        </tr>
                                        @php($i++)
                                    @endforeach
                                    <tr>
                                        <td colspan="5" class="text-right">مجموع وزن / مقدار: {{ $request->commodities->sum('pivot.amount') }}</td>
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
            


<div id="invoice-documentation" class="invoice col-xl-12 box-margin height-card showprint d-none">
    <div class="card card-body">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <img src="{{ asset('img/logo/darklogo.png') }}" class="logo" />
                    <div class="text-center">
                        <h4>خروج کالا از انبار</h4>
                        <div class="factor documentation">( نسخه حسابداری )</div>
                    </div>
                    <div>تاریخ: <span>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}</span></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>خریدار/ نماینده خریدار: <span>{{ $request->customer ? $request->customer->name : 'نامشخص' }}</span></div>
                    <div>شماره درخواست: <span>{{ $request->number }}</span></div>
                </div>
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <table style="border: none;">
                        <tr style="border: none;">
                            <td style="border: none;"></td>
                            <td class="text-left" style="border: none; font-weight: bold;">استان: <span>{{ $request->customer ? $request->customer->province : '' }}</span></td>
                            <td style="border: none;"></td>
                            <td style="border: none; font-weight: bold;">شهر: <span>{{ $request->customer ? $request->customer->city : '' }}</span></td>
                            <td style="border: none;"></td>
                            <td style="border: none;"></td>
                            <td style="border: none;"></td>
                        </tr>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <table class="table-borderless" style="border: 0.5px solid #e0e0e0;">
                        <colgroup>
                            <col span="1" style="width: 5%;">
                            <col span="1" style="width: 8%;">
                            <col span="1" style="width: 40%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 13%;">
                            <col span="1" style="width: 14%;">
                        </colgroup>
                        <thead>
                            <tr class="table-header">
                                <th scope="col">ردیف</th>
                                <th scope="col">برند</th>
                                <th scope="col">مدل</th>
                                <th scope="col">واحد</th>
                                <th scope="col">تعداد</th>
                                <th scope="col">فی(ریال)</th>
                                <th scope="col">جمع(ریال)</th>
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
                                    <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->price) : '-' }}</td>
                                    <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->amount * $commodity->pivot->price) : '-' }}</td>
                                </tr>
                                @php($i++)
                            @endforeach
                            <tr>
                                <td colspan="5" class="text-right">مجموع وزن / مقدار: {{ $request->commodities->sum('pivot.amount') }}</td>
                                <td colspan="2" class="text-right">مجموع: {{ isset($request->total_price) && isset($request->total_price['number']) ? number_format($request->total_price['number']) : '0' }}</td>
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

<div id="invoice-warehouse" class=" invoice col-xl-12 box-margin height-card showprint d-none">
    <div class="card card-body">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <img src="{{ asset('img/logo/darklogo.png') }}" class="logo" />
                    <div class="text-center">
                        <h4>خروج کالا از انبار</h4>
                        <div class="factor warehouse">( نسخه بارگیری )</div>
                    </div>
                    <div>تاریخ: <span>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}</span></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>خریدار/ نماینده خریدار: <span>{{ $request->customer ? $request->customer->name : 'نامشخص' }}</span></div>
                    <div>شماره درخواست: <span>{{ $request->number }}</span></div>
                </div>
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <table style="border: none;">
                        <tr style="border: none;">
                            <td style="border: none;"></td>
                            <td class="text-left" style="border: none; font-weight: bold;">استان: <span>{{ $request->customer ? $request->customer->province : '' }}</span></td>
                            <td style="border: none;"></td>
                            <td style="border: none; font-weight: bold;">شهر: <span>{{ $request->customer ? $request->customer->city : '' }}</span></td>
                            <td style="border: none;"></td>
                            <td style="border: none;"></td>
                            <td style="border: none;"></td>
                        </tr>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <table class="table-borderless" style="border: 0.5px solid #e0e0e0;">
                        <colgroup>
                            <col span="1" style="width: 5%;">
                            <col span="1" style="width: 8%;">
                            <col span="1" style="width: 40%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 10%;">
                        </colgroup>
                        <thead>
                            <tr class="table-header">
                                <th scope="col">ردیف</th>
                                <th scope="col">برند</th>
                                <th scope="col">مدل</th>
                                <th scope="col">واحد</th>
                                <th scope="col">تعداد</th>
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
                                </tr>
                                @php($i++)
                            @endforeach
                            <tr>
                                <td colspan="5" class="text-right">مجموع وزن / مقدار: {{ $request->commodities->sum('pivot.amount') }}</td>
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

        <div id="finvoice" class="col-xl-12 box-margin height-card hideprint">
            <div class="card card-body">
                {{-- <h4 class="card-title"></h4> --}}
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="d-flex justify-content-between">
                            <div class="logo"><img src="{{ asset('img/logo/darklogo.png') }}"/></div>
                            <div><h4>صورتحساب فروش کالا</h4></div>
                            <div>
                                <p>شماره فاکتور: <span>{{$request->number}}</span></p>
                                <p>تاریخ: <span>{{\Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at))}}</span></p>
                            </div>
                        </div>
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
                        <div class="d-flex justify-content-center border">
                            <div class="text-dark p-1">مشخصات خریدار</div>
                        </div>
                        <table class="table customerspecs">
                            <tbody>
                            <tr>
                                <td class="text-left">
                                     نام خریدار: <span>{{ $request->customer->name.'-'. $request->customer->comp_name}} </span></td>
                                <td></td>
                                <td></td>
                                <td>شماره اقتصادی: {{$request->customer->economic_code}}</td>
                                <td></td>
                                <td> شماره ملی:{{ $request->customer->national_code}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="text-left">استان: <span>{{ $request->customer->province ?? '' }}</span></td>
                                <td>شهرستان: {{ $request->customer->city ?? '' }}</td>
                                <td></td>
                                <td> کدپستی:{{$request->customer->zip_code}}</td>
                                <td></td>
                                <td>شهر: {{ $request->customer->city ?? '' }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="text-left">آدرس: <span>{{$request->customer->address}} </span></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>تلفن: {{$request->customer->mobile}}</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="factortable table table-bordered text-center">
                            <thead>
                            <tr class="table-secondary">
                                <th scope="col">ردیف</th>
                                <th scope="col">کد کالا</th>
                                <th scope="col">نام کالا</th>
                                <th scope="col">تعداد / مقدار</th>
                                <th scope="col">واحد</th>
                                <th scope="col" colspan="1.5">فی</th>
                                <th scope="col" colspan="1.5">جمع کل</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($i=1)
                            @foreach ($request->commodities as $commodity)
                                <tr>
                                    <th scope="row">{{$i}}</th>
                                    <td>{{$commodity->number}}</td>
                                    <td>{{$commodity->title}}</td>
                                    <td>{{$commodity->pivot->amount}}</td>
                                    <td>{{ $commodity->pivot->unit_id ? (($unit = \App\Models\Unit::find($commodity->pivot->unit_id)) ? $unit->name . ' (' . $unit->symbol . ')' : '-') : '-' }}</td>
                                    @if(isset($commodity->pivot->price))
                                    <td>{{number_format($price=$commodity->pivot->price)}}</td>
                                    <td>{{ number_format($total_price[]=round($commodity->pivot->amount*$price)) }}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif
                                </tr>
                                @php($i++)
                            @endforeach
                            <tr>
                                <td colspan="5" rowspan="3" class="text-left" style="vertical-align: top">
                                    <div class="d-flex justify-content-between">
                                        <span>شرایط و نحوه تسویه: </span>
                                        <span>نقدی <span class="border"
                                                         style="display:inline-block;width:15px;height:15px"></span></span>
                                        <span>غیرنقدی <span class="border"
                                                            style="display:inline-block;width:15px;height:15px"></span></span>
                                    </div>
                                    <p>توضیحات:</p>
                                </td>
                            </tr>
                            <tr>
                            <td colspan="3" class="text-left">مالیات بر ارزش افزوده : %10</td>
                                
                             </tr>
                            <tr>
                                <td colspan="3" class="text-left">جمع کل : 
                                    @if(isset($request->total_price) && isset($request->total_price['number']))
                                         {{ number_format($request->total_price['number']) }}
                                    @else
                                        0
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                @if(isset($request->total_price) && isset($request->total_price['world']))
                                    <td colspan="2" class="text-left">جمع کل به حروف : {{ $request->total_price['world'] }} ریال </td>
                                @else
                                    <td colspan="2" class="text-left">جمع کل به حروف: صفر ریال </td>
                                @endif
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
        <div id="finvoice2" class="col-xl-12 box-margin height-card hideprint d-none">
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
                <div class="d-flex justify-content-center border">
                    <div class="text-dark p-1">مشخصات خریدار</div>
                </div>
                <table class="table customerspecs">
                    <tbody>
                    <tr>
                        <td class="text-left">
                            نام خریدار: <span>{{ $request->customer->name.'-'. $request->customer->comp_name}} </span></td>
                        <td></td>
                        <td></td>
                        <td>شماره اقتصادی: {{$request->customer->economic_code}}</td>
                        <td></td>
                        <td> شماره ملی:{{ $request->customer->national_code}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-left">استان: <span>{{ $request->customer->province ?? '' }}</span></td>
                        <td>شهرستان: {{ $request->customer->city ?? '' }}</td>
                        <td></td>
                        <td> کدپستی:{{$request->customer->zip_code}}</td>
                        <td></td>
                        <td>شهر: {{ $request->customer->city ?? '' }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-left">آدرس: <span>{{$request->customer->address}} </span></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>تلفن: {{$request->customer->mobile}}</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
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
                        <th scope="col">فی</th>
                        <th scope="col">جمع کل</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php($i = 1)
                        @foreach($request->commodities as $commodity)
                            <tr>
                                <td scope="row">{{ $i }}</td>
                                <td>{{ $commodity->number }}</td>
                                <td>{{ $commodity->title }}</td>
                                <td>{{ $commodity->pivot->amount }}</td>
                                <td>{{ $commodity->pivot->unit_id ? (($unit = \App\Models\Unit::find($commodity->pivot->unit_id)) ? $unit->name : 'نامشخص') : 'نامشخص' }}</td>
                                <td>{{ $commodity->pivot->unit_id ? (($unit = \App\Models\Unit::find($commodity->pivot->unit_id)) ? ceil($commodity->pivot->amount / 10) : '-') : '-' }}</td>
                                <td>10</td>
                                <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->price) : '-' }}</td>
                                <td>{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->amount * $commodity->pivot->price) : '-' }}</td>
                            </tr>
                            @php($i++)
                        @endforeach
                        <tr>
                            <td colspan="5" rowspan="4" class="text-left" style="vertical-align: top">
                                <div class="d-flex justify-content-between">
                                    <span>شرایط و نحوه تسویه: </span>
                                    <span>نقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                    <span>غیرنقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                </div>
                                <p>توضیحات:</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-left"> مالیات بر ارزش افزوده : %10 </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-left">جمع کل : {{ isset($request->total_price) && isset($request->total_price['number']) ? number_format($request->total_price['number']) : '0' }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-left">جمع کل به حروف: 
                                @if(isset($request->total_price) && isset($request->total_price['world']))
                                    {{ $request->total_price['world'] }} ریال
                                @else
                                    صفر ریال
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-left" style="height: 120px">مهر و امضای فروشنده:</td>
                            <td colspan="6" class="text-left">مهر و امضای خریدار:</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Add a new div for the tejarat invoice -->
<div id="finvoice-tejarat" class="col-xl-12 box-margin height-card hideprint d-none">
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
                <div class="d-flex justify-content-center border">
                    <div class="text-dark p-1">مشخصات خریدار</div>
                </div>
                <table class="table customerspecs">
                    <tbody>
                    <tr>
                        <td class="text-left">
                             نام خریدار: <span>{{ $request->customer->name.'-'. $request->customer->comp_name}} </span></td>
                        <td></td>
                        <td></td>
                        <td>شماره اقتصادی: {{$request->customer->economic_code}}</td>
                        <td></td>
                        <td> شماره ملی:{{ $request->customer->national_code}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-left">استان: <span>{{ $request->customer->province ?? '' }}</span></td>
                        <td>شهرستان: {{ $request->customer->city ?? '' }}</td>
                        <td></td>
                        <td> کدپستی:{{$request->customer->zip_code}}</td>
                        <td></td>
                        <td>شهر: {{ $request->customer->city ?? '' }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-left">آدرس: <span>{{$request->customer->address}} </span></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>تلفن: {{$request->customer->mobile}}</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <table class="factortable table table-bordered text-center">
                    <thead>
                    <tr class="table-secondary">
                        <th scope="col">ردیف</th>
                        <th scope="col">کد کالا</th>
                        <th scope="col">شناسه کالا</th>
                        <th scope="col">نام کالا</th>
                        <th scope="col">تعداد / مقدار</th>
                        <th scope="col">واحد</th>
                        <th scope="col" colspan="1.5">فی</th>
                        <th scope="col" colspan="1.5">مالیات بر ارزش افزوده</th>
                        <th scope="col" colspan="1.5">جمع کل</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php($i = 1)
                        @foreach($request->commodities as $commodity)
                            <tr>
                                <td scope="row">{{ $i }}</td>
                                <td>{{ $commodity->number }}</td>
                                <td>{{ $commodity->barcode ?? '2923649785421' }}</td>
                                <td>{{ $commodity->title }}</td>
                                <td>{{ number_format($commodity->pivot->amount) }}</td>
                                <td>{{ $commodity->pivot->unit_id ? (($unit = \App\Models\Unit::find($commodity->pivot->unit_id)) ? $unit->name : 'نامشخص') : 'نامشخص' }}</td>
                                <td colspan="1.5">{{ isset($commodity->pivot->price) ? number_format($commodity->pivot->price) : '-' }}</td>
                                <td colspan="1.5">10%</td>
                                <td colspan="1.5">{{ isset($commodity->pivot->price) ? number_format(round($commodity->pivot->amount * $commodity->pivot->price * 1.1)) : '-' }}</td>
                            </tr>
                            @php($i++)
                        @endforeach
                        <tr>
                        <td colspan="5" rowspan="3" class="text-left" style="vertical-align: top">
                            <div class="d-flex justify-content-between">
                                <span>شرایط و نحوه تسویه: </span>
                                <span>نقدی <span class="border"
                                                 style="display:inline-block;width:15px;height:15px"></span></span>
                                <span>غیرنقدی <span class="border"
                                                    style="display:inline-block;width:15px;height:15px"></span></span>
                            </div>
                            <p>توضیحات:</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-left">جمع کل : 
                            @if(isset($request->total_price) && isset($request->total_price['number']))
                                {{ number_format(round($request->total_price['number'] * 1.1)) }}
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-left">جمع کل به حروف:
                            @if(isset($request->total_price) && isset($request->total_price['world']))
                                {{ $request->total_price['world'] }} ریال
                            @else
                                صفر ریال
                            @endif
                        </td>
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
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/entranceinvoice/entranceinvoice.js') }}"></script>
@endsection
