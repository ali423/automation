@extends('layouts.main')
@section('title', 'جزئیات درخواست فروش کالا')

@section('page_styles')
<link rel="stylesheet" href="{{ asset('css/imexport-print.css') }}">
<link rel="stylesheet" href="{{ asset('css/imfactor-print.css') }}">
@endsection

@section('content')
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
                                       value="{{ $request->customer->name}}"
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
                                       @if (isset($request->creator_user)) value="{{ $request->creator_user->full_name }}"
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
                                    @foreach ($commodity->withdrawal_amount as $withdrawal_amount)
                                    <tr>
                                        <th scope="row" id="rownumbers"></th>
                                        <td>{{ $commodity->title }}</td>
                                        <td>{{ number_format($total_amount[$commodity->id][]=$withdrawal_amount['amount']) .' '. __('fields.commodity.units')[$withdrawal_amount['unit']] }}</td>
                                        <td>{{$withdrawal_amount['warehouse']['title']}}</td>
                                        <td>{{ number_format($commodity->pivot->price) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td class="text-right" colspan="4">مجموع:</td>
                                        <td>{{ number_format(array_sum($total_amount[$commodity->id])) .' '. __('fields.commodity.units')[$withdrawal_amount['unit']] }}</td>
                                    </tr>
                                </table>
                            </div>
                        @endforeach
                        @foreach ($request->comments as $comment)
                            <div class="form-group mb-20">
                                <label for="comment"> {{ $comment->user->full_name }} در تاریخ :
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
                                                    <p class="mb-0"> {{ $file->user->full_name }} در تاریخ :
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
                                                <h5 class="card-title">چاپ رسید کالای خروجی</h5>
                                            </div>
                                        </div>
                                        <div class="d-md-flex justify-content-center">
                                            <a href="#" class="factor customerbtn btn btn-secondary m-1"><i
                                                    class="ti-printer font-18"></i> نسخه خریدار</a>
                                            <a href="#" class="factor documentationbtn btn btn-secondary m-1"><i
                                                    class="ti-printer font-18"></i> نسخه پرونده</a>
                                            <a href="#" class="factor warehousebtn btn btn-secondary m-1"><i
                                                    class="ti-printer font-18"></i> نسخه انبار</a>
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
                                                    class="ti-printer font-18"></i>چاپ فاکتور</a>
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
        <div id="invoice" class="col-xl-12 box-margin height-card showprint">
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
                                <div class="d-none factor customer">( رسید خریدار )</div>
                                <div class="d-none factor documentation">( رسید پرونده )</div>
                                <div class="d-none factor warehouse">( رسید انبار )</div>
                            </div>
                            <div>تاریخ: <span>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}</span></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>خریدار/ نماینده خریدار: <span>{{ $request->customer->name }}</span></div>
                            <div>شماره درخواست: <span>{{$request->number}}</span></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <table class="table-borderless">
                                <colgroup>
                                    <col span="1" style="width: 5%;">
                                    <col span="1" style="width: 30%;">
                                    <col span="1" style="width: 25%;">
                                    <col span="1" style="width: 15%;">
                                    <col span="1" style="width: 25%;">
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
                                    @php($amonuts=json_decode($commodity->pivot->amount))
                                    @foreach($amonuts as $key=>$value)
                                <tr>
                                    <th scope="row">{{$i}}</th>
                                    <td>{{$commodity->title}}</td>
                                    <td>{{\App\Models\Warehouse::query()->where('id',$key)->first()->title}}</td>
                                    <td>{{ $value }} {{ __('fields.commodity.units')[$commodity->pivot->unit] }}</td>
                                    <td></td>
                                </tr>
                                        @php($i++)
                                    @endforeach
                                @endforeach
                            </table>
                        </div>
                        <div class="mb-5">
                            اینجانب <span style="display:inline-block;width: 100px;border-bottom:1px dashed #000">&nbsp;</span>
                            راننده خودرو به شماره پلاک <div class="pelak" style="width: 100px">&nbsp;</div>
                            <div class="pelak">&nbsp;&nbsp;</div>
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
                        {{-- <div class="d-flex justify-content-center border">
                            <div class="text-dark p-1">مشخصات فروشنده</div>
                        </div>
                        <table class="table sellerspecs">
                            <tbody>
                            <tr>
                                <td class="text-left">نام فروشنده: شرکت روغن موتور قم<span> </span></td>
                                <td></td>
                                <td></td>
                                <td>شماره اقتصادی:</td>
                                <td></td>
                                <td>شماره ثبت/ شماره ملی:</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="text-left">استان: قم<span> </span></td>
                                <td>شهرستان: قم</td>
                                <td></td>
                                <td>کدپستی:</td>
                                <td></td>
                                <td>شهر:</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="text-left">آدرس: <span> </span></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>تلفن:</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table> --}}
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
                                <th scope="col">فی</th>
                                <th scope="col">جمع کل</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($i=1)
                            @foreach ($request->commodities as $commodity)
                                <tr>
                                    <th scope="row">{{$i}}</th>
                                    <td>{{$commodity->number}}</td>
                                    <td>{{$commodity->title}}</td>
                                    <td>{{$amount=array_sum(json_decode($commodity->pivot->amount,true))}}</td>
                                    <td>{{__('fields.commodity.units')[$commodity->pivot->unit] }}</td>
                                    @if(isset($commodity->pivot->price))
                                    <td>{{number_format($price=$commodity->pivot->price)}}</td>
                                    <td>{{ number_format($total_price[]=round($amount*$price)) }}</td>
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
                                <td colspan="5" class="text-right">جمع کل</td>
                                @if(isset($request->total_price['number']))
                                    <td> {{ optional($request->total_price)['number'] !== null ? number_format($request->total_price['number']) : 0 }}</td>
                                @else
                                    <td>          </td>
                                @endif

                            </tr>
                            <tr>
                                @if(isset($request->total_price['world']))
                                    <td colspan="6" class="text-left">جمع کل به حروف:{{ $request->total_price['world'] }} ریال </td>
                                @else
                                    <td>          </td>
                                @endif
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
