@extends('layouts.main')
@section('title', 'جزئیات درخواست ورود کالا به انبار')

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/imexport-print.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزئیات درخواست ورود کالا به انبار</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail111"> {{ __('fields.status') }}</label>
                                <input type="text" name="status"
                                    value="{{ __('fields.importing_request.status')[$request->status] }}"
                                    class="form-control" id="exampleInputEmail111" placeholder="{{ __('fields.status') }}"
                                    autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail111"> {{ __('fields.created_at') }}</label>
                                <input type="text" name="name"
                                    value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}"
                                    class="form-control" id="exampleInputEmail111"
                                    placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail111"> {{ __('fields.creator') }}</label>
                                <input type="text" name="name"
                                    @if (isset($request->creator_user)) value="{{ $request->creator_user->full_name }}"
                                       @else
                                       value="سیستم" @endif
                                    class="form-control" id="exampleInputEmail111"
                                    placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail111"> {{ __('fields.importing_request.number') }}</label>
                                <input type="text" name="status"
                                       value="{{ $request->number}}"
                                       class="form-control" id="exampleInputEmail111" placeholder="{{ __('fields.importing_request.number') }} }}"
                                       autocomplete="off" disabled>
                            </div>
                        </div>
                        @foreach ($request->commodities as $commodity)
                            <div id="inputFormRow" class="form-row shadow p-4 m-3">
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
                                        value="{{ __('fields.commodity.units')[$commodity->pivot->unit] }}"
                                        id="unit" name="unit" class="form-control" disabled>
                                    <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="warehouse_id"> {{ __('fields.warehouse.name') }}</label>
                                    <select id="warehouse_id" class="form-control" name="warehouse_id[0]" disabled>
                                        @foreach ($warehouses as $warehouse)
                                            @if ($warehouse->id == $commodity->pivot->warehouses_id)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->title }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">{{ __('fields.warehouse.name') }} را انتخاب کنید.</div>
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
                                @if(isset($commodity->pivot->purchase_price))
                                    <div class="form-group col-md-3">
                                        <label for="price"> {{  __('fields.purchase_unit_price') }}</label>
                                        <input type="text" id="price" placeholder="{{number_format($commodity->pivot->purchase_price)}}" class="form-control" disabled>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @foreach ($request->comments as $comment)
                            <div class="form-group mb-20">
                                <label for="comment"> {{ $comment->user->full_name }} در تاریخ :
                                    {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($comment->created_at)) }}</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment" rows="6" disabled>{{ $comment->body }}</textarea>
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
                                            <h5 class="card-title">چاپ رسید کالای ورودی</h5>
                                        </div>
                                    </div>
                                    <div class="d-md-flex justify-content-center">
                                        <a href="#" class="factor customerbtn btn btn-secondary m-1"><i class="ti-printer font-18"></i> نسخه خریدار</a>
                                        <a href="#" class="factor documentationbtn btn btn-secondary m-1"><i class="ti-printer font-18"></i> نسخه پرونده</a>
                                        <a href="#" class="factor warehousebtn btn btn-secondary m-1"><i class="ti-printer font-18"></i> نسخه انبار</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6 mb-1 mb-md-0">
                                <a href="{{ route('importing-request.edit', $request) }}"
                                    class="btn btn-primary">ویرایش</a>
                                <form method="post" action="{{ route('importing-request.destroy', $request) }}"
                                    class="d-inline w-50">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('آیا از حذف این درخواست مطمئن هستید؟');">حذف</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                @if ($request->status == 'awaiting_approval')
                                    <a href="{{ route('approval.importing', $request) }}"
                                        class="btn btn-primary px-1">تایید درخواست</a>
                                    <a href="{{ route('reject.importing', $request) }}" class="btn btn-danger px-1">رد
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
        <div id="invoice" class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                {{-- <h4 class="card-title"></h4> --}}
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <img src="{{ asset('img/logo/darklogo.png') }}" class="logo" />
                            <div class="text-center">
                                <h4>
                                    ورود کالا به انبار
                                </h4>
                                <div class="d-none factor customer">( رسید خریدار )</div>
                                <div class="d-none factor documentation">( رسید پرونده )</div>
                                <div class="d-none factor warehouse">( رسید انبار )</div>
                            </div>
                            <div>تاریخ: <span>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}</span></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>خریدار/ نماینده خریدار: <span>{{ $request->creator_user->full_name }}</span></div>
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
                                @foreach ($request->commodities as $commodity)
                                <tr>
                                    <th scope="row"></th>
                                    <td>{{ $commodity->title }}</td>
                                    <td>{{ $warehouse->title }}</td>
                                    <td>{{ $commodity->pivot->amount }} {{ __('fields.commodity.units')[$commodity->pivot->unit] }}</td>
                                    <td></td>
                                </tr>
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
        <div id="finvoice"><div class="factorbtn d-none"></div></div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/entranceinvoice/entranceinvoice.js') }}"></script>
@endsection
