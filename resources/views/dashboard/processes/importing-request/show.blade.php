@extends('layouts.main')
@section('title', 'جزئیات درخواست خرید کالا')

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/imexport-print.css') }}">
    <link rel="stylesheet" href="{{ asset('css/imfactor-print.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزئیات درخواست خرید کالا</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.status') }}</label>
                                <input type="text" name="status"
                                    value="{{ __('fields.importing_request.status')[$request->status] }}"
                                    class="form-control" id="exampleInputEmail111" placeholder="{{ __('fields.status') }}"
                                    autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.seller') }}</label>
                                <input type="text" name="status"
                                       value="{{ $request->seller->name}}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.seller') }} }}"
                                       autocomplete="off" disabled>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12 row">
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
                                    value="سیستم"
                                    class="form-control" id="exampleInputEmail111"
                                    placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.importing_request.number') }}</label>
                                <input type="text" name="status"
                                       value="{{ $request->number}}"
                                       class="form-control" id="exampleInputEmail111" placeholder="{{ __('fields.importing_request.number') }} }}"
                                       autocomplete="off" disabled>
                            </div>
                        </div>
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
                                        <span>{{ $commodityMainUnit ? number_format($commodityMainUnit['main_unit_amount'], 2) : '-' }} {{ $commodityMainUnit ? $commodityMainUnit['main_unit_name'] . ' (' . $commodityMainUnit['main_unit_symbol'] . ')' : '' }}</span>
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
                                                <h5 class="card-title">چاپ رسید کالای خریداری شده</h5>
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

        <div id="finvoice"><div class="factorbtn d-none"></div></div>
    </div>
    
    <!-- Invoice Templates -->
    @include('dashboard.processes.importing-request.partials.invoice-template', ['invoiceType' => 'customer'])
    @include('dashboard.processes.importing-request.partials.invoice-template', ['invoiceType' => 'documentation'])
    @include('dashboard.processes.importing-request.partials.invoice-template', ['invoiceType' => 'warehouse'])
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
    
    <script>
        // Debug script to check if invoices are working
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Import request page loaded');
            
            // Check if invoice elements exist
            const customerInvoice = document.querySelector("#invoice-customer");
            const documentationInvoice = document.querySelector("#invoice-documentation");
            const warehouseInvoice = document.querySelector("#invoice-warehouse");
            
            console.log('Customer invoice element:', customerInvoice);
            console.log('Documentation invoice element:', documentationInvoice);
            console.log('Warehouse invoice element:', warehouseInvoice);
            
            // Check if buttons exist
            const customerBtn = document.querySelector(".customerbtn");
            const documentationBtn = document.querySelector(".documentationbtn");
            const warehouseBtn = document.querySelector(".warehousebtn");
            
            console.log('Customer button:', customerBtn);
            console.log('Documentation button:', documentationBtn);
            console.log('Warehouse button:', warehouseBtn);
            
            // Test invoice display
            if (customerBtn && customerInvoice) {
                customerBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Customer button clicked');
                    
                    // Hide all invoices first
                    document.querySelectorAll('.invoice').forEach(inv => {
                        inv.style.display = 'none';
                    });
                    
                    // Show customer invoice
                    customerInvoice.style.display = 'block';
                    customerInvoice.style.position = 'fixed';
                    customerInvoice.style.top = '0';
                    customerInvoice.style.left = '0';
                    customerInvoice.style.width = '100%';
                    customerInvoice.style.height = '100%';
                    customerInvoice.style.background = 'white';
                    customerInvoice.style.zIndex = '9999';
                    customerInvoice.style.overflowY = 'auto';
                    customerInvoice.style.padding = '20px';
                    
                    console.log('Customer invoice should be visible now');
                });
            }
            
            if (documentationBtn && documentationInvoice) {
                documentationBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Documentation button clicked');
                    
                    // Hide all invoices first
                    document.querySelectorAll('.invoice').forEach(inv => {
                        inv.style.display = 'none';
                    });
                    
                    // Show documentation invoice
                    documentationInvoice.style.display = 'block';
                    documentationInvoice.style.position = 'fixed';
                    documentationInvoice.style.top = '0';
                    documentationInvoice.style.left = '0';
                    documentationInvoice.style.width = '100%';
                    documentationInvoice.style.height = '100%';
                    documentationInvoice.style.background = 'white';
                    documentationInvoice.style.zIndex = '9999';
                    documentationInvoice.style.overflowY = 'auto';
                    documentationInvoice.style.padding = '20px';
                    
                    console.log('Documentation invoice should be visible now');
                });
            }
            
            if (warehouseBtn && warehouseInvoice) {
                warehouseBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Warehouse button clicked');
                    
                    // Hide all invoices first
                    document.querySelectorAll('.invoice').forEach(inv => {
                        inv.style.display = 'none';
                    });
                    
                    // Show warehouse invoice
                    warehouseInvoice.style.display = 'block';
                    warehouseInvoice.style.position = 'fixed';
                    warehouseInvoice.style.top = '0';
                    warehouseInvoice.style.left = '0';
                    warehouseInvoice.style.width = '100%';
                    warehouseInvoice.style.height = '100%';
                    warehouseInvoice.style.background = 'white';
                    warehouseInvoice.style.zIndex = '9999';
                    warehouseInvoice.style.overflowY = 'auto';
                    warehouseInvoice.style.padding = '20px';
                    
                    console.log('Warehouse invoice should be visible now');
                });
            }
        });
    </script>
@endsection
