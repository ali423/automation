@extends('layouts.main')
@section('title', 'نمایش سفارش')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات سفارش</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="customer_id">{{ __('fields.customer')}}</label>
                                <input type="text" class="form-control" value="{{ $order->customer ? $order->customer->name : 'مشتری حذف شده' }}" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ __('fields.deadline') }}</label>
                                <input type="text" class="form-control" value="{{ $order->deadline }}" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="unit"> {{ __('fields.status') }}</label>
                                <input type="text" class="form-control" value="{{ __('fields.order.status.' . $order->status) }}" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.created_at') }}</label>
                                <input type="text" name="name"
                                       value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($order->created_at)) }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.creator') }}</label>
                                <input type="text" name="name"
                                       value="{{ isset($order->creator_user) ? $order->creator_user->full_name : 'سیستم' }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                        </div>

                        <!-- Order Items -->
                        @include('dashboard.order.partials.order-items-table', [
                            'order' => $order,
                            'showWeight' => true
                        ])
                        @if($order->status =='done')
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('fields.done_date') }}</label>
                                    <input type="text"  class="form-control" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($order->updated_at)) }}" disabled>
                                </div>
                                @if($order->withdrawalRequest)
                                <div class="form-group col-md-8">
                                    <label>اطلاعات راننده</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control mb-2" value="{{ $order->withdrawalRequest->driver_name ?? 'نامشخص' }}" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control mb-2" value="{{ $order->withdrawalRequest->driver_phone ?? '-' }}" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control mb-2" value="{{ trim(($order->withdrawalRequest->plate_serial ?? '') . ' ' . ($order->withdrawalRequest->plate_number ?? '')) ?: '-' }}" disabled>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                        @include('dashboard.order.partials.order-action-buttons', [
                            'order' => $order,
                            'showEdit' => true,
                            'showDelete' => true,
                            'showConfirm' => true
                        ])

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.order.partials.proforma-invoice', ['order' => $order])
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script>
        function showProformaInvoice(invoiceId) {
            var el = document.getElementById(invoiceId);
            if (!el) return;
            el.style.display = 'block';
            el.style.position = 'fixed';
            el.style.top = '0';
            el.style.left = '0';
            el.style.width = '100%';
            el.style.height = '100%';
            el.style.background = 'white';
            el.style.zIndex = '10000';
            el.style.overflowY = 'auto';
            el.style.padding = '20px';
            var closer = document.getElementById('proforma-invoice-close');
            if (closer) closer.style.display = 'block';
        }
        function hideProformaInvoice(invoiceId) {
            var el = document.getElementById(invoiceId);
            if (!el) return;
            el.style.display = 'none';
            var closer = document.getElementById('proforma-invoice-close');
            if (closer) closer.style.display = 'none';
        }
        // Print proforma invoice function
        function printProformaInvoice(invoiceId) {
            // Get the invoice content
            var invoiceElement = document.getElementById(invoiceId);
            if (!invoiceElement) {
                alert('خطا: عنصر پیش فاکتور یافت نشد');
                return;
            }
            
            // Check if invoice has content
            if (!invoiceElement.innerHTML || invoiceElement.innerHTML.trim() === '') {
                alert('خطا: محتوای پیش فاکتور خالی است');
                return;
            }
            
            // Get the invoice content
            var printContents = invoiceElement.innerHTML;
            
            try {
                // Try popup method first
                var printWindow = window.open('', '_blank', 'width=800,height=600');
                if (printWindow) {
                    // Create the print document
                    var printDocument = printWindow.document;
                    printDocument.write('<!DOCTYPE html>');
                    printDocument.write('<html dir="rtl" lang="fa">');
                    printDocument.write('<head>');
                    printDocument.write('<meta charset="UTF-8">');
                    printDocument.write('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
                    printDocument.write('<title>پیش فاکتور - {{ $order->customer ? ($order->customer->name . (isset($order->customer->comp_name) && $order->customer->comp_name ? ' - ' . $order->customer->comp_name : '')) : 'بدون‌نام' }} - {{ \Morilog\Jalali\CalendarUtils::strftime('Y-m-d', strtotime($order->created_at)) }} - سفارش {{ $order->id }}</title>');
                    printDocument.write('<style>');
                    printDocument.write('@media print { body { margin: 0; padding: 20px; } .no-print { display: none !important; } }');
                    printDocument.write('body { font-family: "Tahoma", "Arial", sans-serif; direction: rtl; text-align: right; margin: 0; padding: 20px; background: white; }');
                    printDocument.write('.table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }');
                    printDocument.write('.table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: center; }');
                    printDocument.write('.table th { background-color: #f8f9fa !important; font-weight: bold; }');
                    printDocument.write('.text-left { text-align: right !important; }');
                    printDocument.write('.text-center { text-align: center !important; }');
                    printDocument.write('.border { border: 1px solid #000; }');
                    printDocument.write('.logo img { max-width: 120px; height: auto; }');
                    printDocument.write('.customerspecs td { padding: 5px; }');
                    printDocument.write('.factortable th, .factortable td { padding: 8px; }');
                    printDocument.write('.card { border: none !important; box-shadow: none !important; }');
                    printDocument.write('.card-body { padding: 0 !important; }');
                                         printDocument.write('@media print { .btn { display: none !important; } .card { border: none !important; } }');
                     printDocument.write('.factortable { page-break-inside: avoid; }');
                     printDocument.write('.customerspecs { page-break-inside: avoid; }');
                     printDocument.write('tr { page-break-inside: avoid; }');
                     printDocument.write('.table th, .table td { padding: 4px !important; font-size: 11px !important; }');
                     printDocument.write('.customerspecs td { padding: 3px !important; font-size: 11px !important; }');
                     printDocument.write('body { margin: 10px !important; padding: 10px !important; }');
                    printDocument.write('</style>');
                    printDocument.write('</head>');
                    printDocument.write('<body>');
                    printDocument.write(printContents);
                    printDocument.write('<script>');
                    printDocument.write('window.onload = function() { setTimeout(function() { window.print(); }, 500); };');
                    printDocument.write('window.onafterprint = function() { window.close(); };');
                    printDocument.write('setTimeout(function() { if (!window.closed) { window.close(); } }, 10000);');
                    printDocument.write('<\/script>');
                    printDocument.write('</body>');
                    printDocument.write('</html>');
                    
                    printDocument.close();
                } else {
                    // Fallback to direct print method
                    fallbackPrint(invoiceElement);
                }
            } catch (error) {
                // Fallback to direct print method
                fallbackPrint(invoiceElement);
            }
        }
        
        // Fallback print function
        function fallbackPrint(invoiceElement) {
            // Store original body content
            var originalBody = document.body.innerHTML;
            var originalTitle = document.title;
            
            try {
                // Replace body content with invoice
                document.body.innerHTML = invoiceElement.innerHTML;
                document.title = "پیش فاکتور - {{ $order->customer ? ($order->customer->name . (isset($order->customer->comp_name) && $order->customer->comp_name ? ' - ' . $order->customer->comp_name : '')) : 'بدون‌نام' }} - {{ \Morilog\Jalali\CalendarUtils::strftime('Y-m-d', strtotime($order->created_at)) }} - سفارش {{ $order->id }}";
                
                // Add print styles
                var style = document.createElement('style');
                                 style.textContent = 'body { font-family: "Tahoma", "Arial", sans-serif; direction: rtl; text-align: right; margin: 10px; padding: 10px; background: white; } .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; } .table th, .table td { border: 1px solid #ddd; padding: 4px; text-align: center; font-size: 11px; } .table th { background-color: #f8f9fa !important; font-weight: bold; } .text-left { text-align: right !important; } .text-center { text-align: center !important; } .border { border: 1px solid #000; } .logo img { max-width: 120px; height: auto; } .customerspecs td { padding: 3px; font-size: 11px; } .factortable th, .factortable td { padding: 4px; font-size: 11px; } .card { border: none !important; box-shadow: none !important; } .card-body { padding: 0 !important; } .factortable { page-break-inside: avoid; } .customerspecs { page-break-inside: avoid; } tr { page-break-inside: avoid; } @media print { .btn { display: none !important; } }';
                document.head.appendChild(style);
                
                // Print
                window.print();
                
                // Restore original content after a delay
                setTimeout(function() {
                    document.body.innerHTML = originalBody;
                    document.title = originalTitle;
                    // Re-run any necessary scripts
                    if (typeof initializePage === 'function') {
                        initializePage();
                    }
                }, 1000);
                
            } catch (error) {
                // Restore original content immediately
                document.body.innerHTML = originalBody;
                document.title = originalTitle;
                alert('خطا در چاپ پیش فاکتور. لطفاً دوباره تلاش کنید.');
            }
        }
        
        // Make functions globally available
        window.printProformaInvoice = printProformaInvoice;
        window.fallbackPrint = fallbackPrint;
        window.showProformaInvoice = showProformaInvoice;
        window.hideProformaInvoice = hideProformaInvoice;
    </script>
@endsection
