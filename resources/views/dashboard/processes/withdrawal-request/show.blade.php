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
                                <label for="status">{{ __('fields.status') }}</label>
                                <input type="text" name="status"
                                       value="{{ $request->status ? __('fields.withdrawal-request.status')[$request->status] : '-' }}"
                                       class="form-control" id="status"
                                       placeholder="{{ __('fields.status') }}"
                                       autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="customer">{{ __('fields.customer') }}</label>
                                <input type="text" name="customer"
                                       value="{{ $request->customer ? $request->customer->name : 'نامشخص' }}"
                                       class="form-control" id="customer"
                                       placeholder="{{ __('fields.customer') }}"
                                       autocomplete="off" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="created_at">{{ __('fields.created_at') }}</label>
                                <input type="text" name="created_at"
                                       value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}"
                                       class="form-control" id="created_at"
                                       placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="creator">{{ __('fields.creator') }}</label>
                                <input type="text" name="creator"
                                       value="سیستم"
                                       class="form-control" id="creator"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="request_number">{{ __('fields.importing_request.number') }}</label>
                                <input type="text" name="request_number"
                                       value="{{ $request->number}}"
                                       class="form-control" id="request_number"
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
                                    <input type="text" value="{{ $commodity->title }}" class="form-control" disabled>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="unit"> {{ __('fields.unit') }}</label>
                                    <input type="text"
                                        value="{{ $commodity->pivot->unit_id ? (\App\Models\Unit::find($commodity->pivot->unit_id)->name . ' (' . \App\Models\Unit::find($commodity->pivot->unit_id)->symbol . ')') : '-' }}"
                                        class="form-control" disabled>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="amount"> {{ __('fields.commodity.amount') }}</label>
                                    <input type="text" value="{{ $commodity->pivot->amount }}" class="form-control" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="pieces_per_box">تعداد در کارتن</label>
                                    <input type="text" value="{{ $commodity->pivot->pieces_per_box ?? 1 }}" class="form-control" disabled>
                                </div>
                                @if(isset($commodity->pivot->price))
                                    <div class="form-group col-md-4">
                                        <label for="price"> {{  __('fields.sell-price_per_unit') }}</label>
                                        <input type="text" value="{{ number_format($commodity->pivot->price) }}" class="form-control" disabled>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Invoice Templates -->
    @include('dashboard.processes.withdrawal-request.partials.invoice-template', ['invoiceType' => 'customer'])
    @include('dashboard.processes.withdrawal-request.partials.invoice-template', ['invoiceType' => 'documentation'])
    @include('dashboard.processes.withdrawal-request.partials.invoice-template', ['invoiceType' => 'warehouse'])
    
    <!-- Financial Invoice -->
    @include('dashboard.processes.withdrawal-request.partials.financial-invoice')
    
    <!-- Tejarat Invoice -->
    @include('dashboard.processes.withdrawal-request.partials.tejarat-invoice')
    
    <!-- Main Invoice Container -->
    <div id="finvoice"><div class="factorbtn d-none"></div></div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/entranceinvoice/entranceinvoice.js') }}"></script>
    
    <style>
        /* Position invoices absolutely when shown to prevent them from affecting page layout */
        .invoice.showprint, #finvoice2.showprint, #finvoice-tejarat.showprint {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: white !important;
            z-index: 9999 !important;
            overflow-y: auto !important;
            padding: 20px !important;
        }
        
        /* Ensure invoices are hidden by default */
        .invoice, #finvoice2, #finvoice-tejarat {
            display: none !important;
        }
        
        /* Override the showprint display for proper positioning */
        .invoice.showprint, #finvoice2.showprint, #finvoice-tejarat.showprint {
            display: block !important;
        }
    </style>
@endsection
