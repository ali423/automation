@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')
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
                                <div class="form-group col-md-6">
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
                                        <p> مقدار {{ number_format($withdrawal_amount['amount']) .' '. __('fields.commodity.units')[$withdrawal_amount['unit']] }} از
                                            انبار {{$withdrawal_amount['warehouse']['title']}}</p>
                                        <br>
                                    @endforeach
                                </div>
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
                                                <h5 class="card-title">چاپ رسید کالای ورودی</h5>
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
        <div id="invoice" class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                {{-- <h4 class="card-title"></h4> --}}
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="d-flex justify-content-between">
                            <div class="logo"><img src="{{ asset('img/logo/darklogo.png') }}" /></div>
                            <div><h4>صورتحساب فروش کالا و خدمات</h4></div>
                            <div>
                                <p>شماره فاکتور: <span>12345555559</span></p>
                                <p>تاریخ: <span>1399/9/9</span></p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center border">
                            <div class="text-dark p-1">مشخصات فروشنده</div>
                        </div>
                        <table class="table sellerspecs">
                            <tbody>
                              <tr>
                                <td class="text-left">نام فروشنده: <span> </span></td>
                                <td></td>
                                <td></td>
                                <td>شماره اقتصادی:</td>
                                <td></td>
                                <td>شماره ثبت/ شماره ملی:</td>
                                <td></td>
                              </tr>
                              <tr>
                                <td class="text-left">استان: <span> </span></td>
                                <td>شهرستان:</td>
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
                        </table>
                        <div class="d-flex justify-content-center border">
                            <div class="text-dark p-1">مشخصات خریدار</div>
                        </div>
                        <table class="table customerspecs">
                            <tbody>
                              <tr>
                                <td class="text-left">نام فروشنده: <span> </span></td>
                                <td></td>
                                <td></td>
                                <td>شماره اقتصادی:</td>
                                <td></td>
                                <td>شماره ثبت/ شماره ملی:</td>
                                <td></td>
                              </tr>
                              <tr>
                                <td class="text-left">استان: <span> </span></td>
                                <td>شهرستان:</td>
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
                                <th scope="col">مبلغ کل</th>
                                <th scope="col">تخفیف</th>
                                <th scope="col">مبلغ کل پس از تخفیف</th>
                                <th scope="col">مجموع مالیات</th>
                                <th scope="col">جمع کل</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th scope="row">1</th>
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                                <td>@mdo</td>
                              </tr>
                              <tr>
                                <td colspan="3" class="text-right">جمع</td>
                                <td>45</td>
                                <td></td>
                                <td></td>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                              </tr>
                              <tr>
                                <td colspan="5" rowspan="3" class="text-left" style="vertical-align: top">
                                    <div class="d-flex justify-content-between">
                                        <span>شرایط و نحوه تسویه: </span>
                                        <span>نقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                        <span>غیرنقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                    </div>
                                    <p>توضیحات:</p>
                                </td>
                                <td colspan="5" class="text-right">مبلغ هزینه</td>
                                <td></td>
                              </tr>
                              <tr>
                                <td colspan="5" class="text-right">جمع کل</td>
                                <td></td>
                              </tr>
                              <tr>
                                <td colspan="6" class="text-left">جمع کل به حروف:</td>
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
