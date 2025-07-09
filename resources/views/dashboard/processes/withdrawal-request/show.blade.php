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
                                       value="{{ __('fields.withdrawal-request.status')[$request->status] }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.status') }}"
                                       autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.customer') }}</label>
                                <input type="text" name="status"
                                       value="{{ $request->customer ? $request->customer->name : 'نامشخص' }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.customer') }} }}"
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
                                       @if (isset($request->creator_user) && $request->creator_user) value="{{ $request->creator_user->full_name }}"
                                       @else
                                       value="سیستم" @endif
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.importing_request.number') }}</label>
                                <input type="text" name="status"
                                       value="{{ $request->number}}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.importing_request.number') }} }}"
                                       autocomplete="off" disabled>
                            </div>
                        </div>
                        @php($i=1)
                        @php($total_amount = 0)
                        @foreach ($request->commodities as $commodity)
                            <div id="inputFormRow" class="form-row shadow p-4 m-3">
                                {{-- <div class="form-group col-md-6">
                                    <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
                                    <select id="commodity_id" class="form-control" name="commodity_id[0]" disabled>
                                        <option value="{{ $commodity->id }}">{{ $commodity->title }}</option>
                                    </select>
                                    <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="unit"> {{ __('fields.unit') }}</label>
                                    <input type="text"
                                           value="{{ __('fields.commodity.units')[$commodity->pivot->unit] }}"
                                           id="unit" name="unit" class="form-control" disabled>
                                    <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    @foreach ($commodity->withdrawal_amount as $withdrawal_amount)
                                        <p>
                                            مقدار {{ number_format($withdrawal_amount['amount']) .' '. __('fields.commodity.units')[$withdrawal_amount['unit']] }}
                                            از
                                            انبار {{$withdrawal_amount['warehouse']['title']}}</p>
                                        <br>
                                    @endforeach
                                </div> --}}
                                <table class="table">
                                    <colgroup>
                                        <col span="1" style="width: 5%;">
                                        <col span="1" style="width: 40%;">
                                        <col span="1" style="width: 25%;">
                                        <col span="1" style="width: 30%;">
                                    </colgroup>
                                    <tr class="table-header table-dark">
                                        <th scope="col">ردیف</th>
                                        <th scope="col">نام کالا</th>
                                        <th scope="col">مقدار</th>
                                        <th scope="col">انبار</th>
                                        <th scope="col">قسمت فروش (ریال)</th>
                                    </tr>
                                    @php($amonuts=json_decode($commodity->pivot->amount))
                                    @foreach($amonuts as $key=>$value)
                                        @php($total_amount += $value)
                                        <tr>
                                            <th scope="row">{{$i}}</th>
                                            <td>{{ $commodity->title }}</td>
                                            <td>{{ $value }} {{ __('fields.commodity.units')[$commodity->pivot->unit] }}</td>
                                            <td>{{ $commodity->withdrawal_amount[0]['warehouse']['title'] ?? 'نامشخص' }}</td>
                                            <td>{{ number_format($commodity->pivot->price) }}</td>
                                        </tr>
                                        @php($i++)
                                    @endforeach
                                </table>
                            </div>
                        @endforeach
                        <tr>
                            <td colspan="5" class="text-right">مجموع وزن: {{$total_amount}} {{ __('fields.commodity.units')[$commodity->pivot->unit] }}</td>
                        </tr>
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
                                <thead>
                                    <tr class="table-header">
                                        <th scope="col">ردیف</th>
                                        <th scope="col">برند</th>
                                        <th scope="col">مدل</th>
                                        <th scope="col">واحد</th>
                                        <th scope="col">تعداد</th>
                                        @if($receiptType == 'documentation')
                                            <th scope="col">فی(ریال)</th>
                                            <th scope="col">جمع(ریال)</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($i=1)
                                    @php($total_amount = 0)
                                    @foreach($request->commodities as $commodity)
                                        @php($amonuts=json_decode($commodity->pivot->amount))
                                        @foreach($amonuts as $key=>$value)
                                            @php($total_amount += $value)
                                            <tr>
                                                <td scope="row">{{$i}}</td>
                                                <td>{{$commodity->title}}</td>
                                                <td>{{\App\Models\Warehouse::query()->where('id',$key)->first()->title}}</td>
                                                <td>{{ $value }} {{ __('fields.commodity.units')[$commodity->pivot->unit] }}</td>
                                                <td></td>
                                                @if($receiptType == 'documentation')
                                                    <td></td>
                                                    <td></td>
                                                @endif
                                            </tr>
                                            @php($i++)
                                        @endforeach
                                    @endforeach
                                    <tr>
                                        <td colspan="@if($receiptType == 'documentation') 7 @else 5 @endif" class="text-right">مجموع وزن / مقدار: {{$total_amount}} {{ __('fields.commodity.units')[$commodity->pivot->unit] }}</td>
                                    </tr>
                                </tbody>
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
                                    <td class="text-left" style="border: none; font-weight: bold;">استان: <span></span></td>
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
                                <tbody >
                                    <tr >
                                        <td scope="row">1</td>
                                        <td>زیگما</td>
                                        <td style="text-align: center;">موتور چهار لیتری پلاستیکی SAE : 20w50</td>
                                        <td>کارتن</td>
                                        <td>400</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>زیگما</td>
                                        <td style="text-align: center;">موتور یک لیتری پلاستیکی SAE : 50</td>
                                        <td>کارتن</td>
                                        <td>50</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>زیگما</td>
                                        <td style="text-align: center;">گریس</td>
                                        <td>کارتن</td>
                                        <td>400</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right">مجموع وزن / مقدار: 850 </td>
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
                            <td class="text-left" style="border: none; font-weight: bold;">استان: <span></span></td>
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
                            <tr >
                                        <td scope="row">1</td>
                                        <td>زیگما</td>
                                        <td style="text-align: center;">موتور چهار لیتری پلاستیکی SAE : 20w50</td>
                                        <td>کارتن</td>
                                        <td>400</td>
                                        <td>450000</td>
                                        <td>180000000</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>زیگما</td>
                                        <td style="text-align: center;">موتور یک لیتری پلاستیکی SAE : 50</td>
                                        <td>کارتن</td>
                                        <td>50</td>
                                        <td>400000</td>
                                        <td>2000000</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>زیگما</td>
                                        <td style="text-align: center;">گریس</td>
                                        <td>کارتن</td>
                                        <td>400</td>
                                        <td>630000</td>
                                        <td>252000000</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right">مجموع وزن / مقدار: 850 </td>
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
                            <td class="text-left" style="border: none; font-weight: bold;">استان: <span></span></td>
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
                            <tr >
                                        <td scope="row">1</td>
                                        <td>زیگما</td>
                                        <td style="text-align: center;">موتور چهار لیتری پلاستیکی SAE : 20w50</td>
                                        <td>کارتن</td>
                                        <td>400</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>زیگما</td>
                                        <td style="text-align: center;">موتور یک لیتری پلاستیکی SAE : 50</td>
                                        <td>کارتن</td>
                                        <td>50</td>
                                    </tr>
                                    <tr>
                                        <td scope="row">1</td>
                                        <td>زیگما</td>
                                        <td style="text-align: center;">گریس</td>
                                        <td>کارتن</td>
                                        <td>400</td>

                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right">مجموع وزن / مقدار: 850 </td>
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
                                <td class="text-left">استان: <span> </span></td>
                                <td>شهرستان:</td>
                                <td></td>
                                <td> کدپستی:{{$request->customer->zip_code}}</td>
                                <td></td>
                                <td>شهر:</td>
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
                                @foreach($request->commodities as $commodity)
                                    @php($amount = array_sum(json_decode($commodity->pivot->amount, true)))
                                    <tr>
                                        <!-- <td scope="row">{{$i}}</td>
                                        <td>{{$commodity->number}}</td>
                                        <td>{{$commodity->title}}</td>
                                        <td>{{$amount}}</td>
                                        <td>{{__('fields.commodity.units')[$commodity->pivot->unit] }}</td>
                                        <td colspan="1.5"></td>
                                        <td colspan="1.5"></td> -->
                                        
                                        <td scope="row">1</td>
                                        <td>8728028</td>
                                        <td>موتور چهارلیتری پلاستیکی SAE:20w50</td>
                                        <td>400</td>
                                        <td>کارتن</td>
                                        <td colspan="1.5">450,000</td>
                                        <td colspan="1.5">180,000,000</td>
                                </tr>
                                <tr>
                                        <td scope="row">2</td>
                                        <td>8728029</td>
                                        <td>موتور یک لیتری پلاستیکی SAE:50</td>
                                        <td>50</td>
                                        <td>کارتن</td>
                                        <td colspan="1.5">400,000</td>
                                        <td colspan="1.5">20,000,000</td>
                                </tr>
                                <tr>
                                        <td scope="row">3</td>
                                        <td>8728030</td>
                                        <td>گریس</td>
                                        <td>400</td>
                                        <td>کارتن</td>
                                        <td colspan="1.5">630,000</td>
                                        <td colspan="1.5">252,000,000</td>                                        
                                        
                                    </tr>
                                    @php($i++)
                                @endforeach

                                <tr>
                                <td colspan="5" rowspan="4" class="text-left" style="vertical-align: top">
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
                                    497,200,000

                                <!-- @if(isset($request->total_price) && isset($request->total_price['number']))
                                     {{ number_format($request->total_price['number']) }}
                                @else
                                    
                                @endif -->

                                    </td>
                            </tr>
                            <tr>
                                <!-- @if(isset($request->total_price) && isset($request->total_price['world']))
                                    <td colspan="2" class="text-left">جمع کل به حروف : {{ $request->total_price['world'] }} ریال </td>
                                @else
                                    <td colspan="2" class="text-left">جمع کل به حروف: صفر ریال </td>
                                @endif -->

                                <td colspan="2" class="text-left">جمع کل به حروف : چهارصد و نود و هفت میلیون و دویست هزار ریال</td>
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
                        <td class="text-left">استان: <span> </span></td>
                        <td>شهرستان:</td>
                        <td></td>
                        <td> کدپستی:{{$request->customer->zip_code}}</td>
                        <td></td>
                        <td>شهر:</td>
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
                        <!-- @php($i=1)
                        @foreach($request->commodities as $commodity)
                            @php($amount = array_sum(json_decode($commodity->pivot->amount, true)))
                            <tr>
                                <td scope="row">{{$i}}</td>
                                <td>{{$commodity->number}}</td>
                                <td>{{$commodity->title}}</td>
                                <td>{{$amount}}</td>
                                <td>{{__('fields.commodity.units')[$commodity->pivot->unit] }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php($i++)
                        @endforeach -->
                        <tr>
                            <td scope="row">1</td>
                            <td>8728028</td>
                            <td>موتور چهارلیتری پلاستیکی SAE:20w50</td>
                            <td>400</td>
                            <td>کارتن</td>
                            <td>40</td>
                            <td>10</td>
                            <td>450,000</td>
                            <td>180,000,000</td>
                        </tr>
                        <tr>
                            <td scope="row">2</td>
                            <td>8728029</td>
                            <td>موتور یک لیتری پلاستیکی SAE:50</td>
                            <td>50</td>
                            <td>کارتن</td>
                            <td>5</td>
                            <td>10</td>
                            <td>400,000</td>
                            <td>20,000,000</td>
                        </tr>
                        <tr>
                            <td scope="row">3</td>
                            <td>8728030</td>
                            <td>گریس</td>
                            <td>400</td>
                            <td>کارتن</td>
                            <td>40</td>
                            <td>10</td>
                            <td>630,000</td>
                            <td>252,000,000</td>
                        </tr>
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
                            <td colspan="4" class="text-left">جمع کل : 497,200,000</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-left">جمع کل به حروف: چهارصد و نود و هفت میلیون و دویست هزار ریال </td>
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

    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/entranceinvoice/entranceinvoice.js') }}"></script>
@endsection
