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
                            // Pre-calculate main unit data once for all commodities
                            $mainUnitData = $request->getMainUnitAmountAttribute();
                        @endphp
                        @foreach ($request->commodities as $commodity)
                            @include('dashboard.processes.withdrawal-request.partials.commodity-display', [
                                'commodity' => $commodity,
                                'mainUnitData' => $mainUnitData
                            ])
                        @endforeach

                        
                        @include('dashboard.processes.withdrawal-request.partials.comments', ['comments' => $request->comments])
                        @include('dashboard.processes.withdrawal-request.partials.file-attachments', ['files' => $request->files])
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
                        @include('dashboard.processes.withdrawal-request.partials.action-buttons', ['request' => $request])
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
            overflow-x: hidden !important;
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
        
        /* Fix for tejarat invoice to prevent cropping and scrollbar issues */
        #finvoice-tejarat.showprint {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        
        #finvoice-tejarat.showprint .card {
            margin: 0 !important;
            border: none !important;
        }
        
        #finvoice-tejarat.showprint .card-body {
            padding: 15px !important;
        }
    </style>
@endsection
