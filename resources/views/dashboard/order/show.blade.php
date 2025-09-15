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
                                       value="سیستم"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                        </div>

                        <!-- Order Items -->
                        @if($order->orderItems->count() > 0)
                            <div class="form-row mt-4">
                                <div class="col-12">
                                    <h5>جزئیات سفارش</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ردیف</th>
                                                    <th>کالا</th>
                                                    <th>مقدار</th>
                                                    <th>واحد</th>
                                                    <th>قیمت واحد</th>
                                                    <th>قیمت کل</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->orderItems as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $item->commodity ? $item->commodity->title : 'کالا حذف شده' }}</td>
                                                        <td>{{ number_format($item->commodity_amount) }}</td>
                                                        <td>{{ $item->unit_symbol }}</td>
                                                        <td>{{ number_format($item->price) }} تومان</td>
                                                        <td>{{ number_format($item->total_price) }} تومان</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="5" class="text-left">مجموع کل:</th>
                                                    <th>{{ number_format($order->total_price) }} تومان</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($order->status =='done')
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('fields.done_date') }}</label>
                                    <input type="text"  class="form-control" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($order->updated_at)) }}" disabled>
                                </div>
                            </div>
                            @endif

                        <div class="row">
                            <div class="col-md-6">
                                @if($order->status !== 'done')
                                    <a href="{{ route('order.edit', $order) }}" class="btn btn-primary">ویرایش</a>
                                    <form method="post" action="{{ route('order.destroy', $order) }}" class="d-inline w-50">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('آیا از حذف این سفارش مطمئن هستید؟');">حذف سفارش</button>
                                    </form>
                                @else
                                    <span class="text-muted">سفارش تحویل شده - امکان ویرایش و حذف وجود ندارد</span>
                                @endif
                            </div>
                            <div class="col-md-6 text-md-right">
                                @if($order->status !== 'done')
                                    <a href="{{ route('order.confirm', $order) }}" class="btn btn-success">تحویل سفارش</a>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Proforma Invoice Download Button -->
    <div class="row mt-2">
    </div>
    <!-- Proforma Invoice Print Card -->
    <div class="col-xl-12 height-card box-margin">
        <div class="card">
            <div class="card-body">
                <div class="bg-transparent d-flex align-items-center justify-content-between">
                    <div class="widgets-card-title">
                        <h5 class="card-title">چاپ پیش فاکتور</h5>
                    </div>
                </div>
                <div class="d-md-flex justify-content-center">
                    <button type="button" class="factor factorbtn btn btn-secondary m-1" onclick="printProformaInvoice('proforma-invoice-2')">
                        <i class="ti-printer font-18"></i> چاپ پیش فاکتور
                    </button>
                    <button type="button" class="btn btn-secondary m-1" onclick="showProformaInvoice('proforma-invoice-2')">
                        <i class="ti-eye font-18"></i> نمایش پیش فاکتور
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Hidden Proforma Invoice Section -->
    <div id="proforma-invoice-close" style="display:none; position: fixed; top: 10px; left: 10px; z-index: 10001;">
        <button type="button" class="btn btn-danger btn-sm" onclick="hideProformaInvoice('proforma-invoice-2')">بستن</button>
    </div>
    <div id="proforma-invoice-2" style="display:none;">
        <div class="row mt-4">
            <div class="col-xl-12 box-margin height-card">
                <div class="card card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="d-flex justify-content-between">
                                <div class="logo"><img src="{{ asset('img/logo/darklogo.png') }}" style="width: 120px; height: auto;"/></div>
                                <div><h4>پیش فاکتور</h4></div>
                                <div>
                                    <p>شماره پیش فاکتور: <span>{{$order->id}}</span></p>
                                    <p>تاریخ: <span>{{\Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($order->created_at))}}</span></p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center border mt-3 mb-2">
                                <div class="text-dark p-1">مشخصات خریدار</div>
                            </div>
                            <table class="table customerspecs" style="font-size: 11px; margin-bottom: 5px;">
                                <tbody>
                                <tr>
                                    <td class="text-left">
                                         نام خریدار: <span>{{ $order->customer ? $order->customer->name.'-'.($order->customer->comp_name ?? '') : ''}} </span></td>
                                    <td>شماره اقتصادی: {{$order->customer->economic_code ?? ''}}</td>
                                    <td>شماره ملی: {{$order->customer->national_code ?? ''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">استان: <span>{{ $order->customer->province ?? '' }}</span></td>
                                    <td>شهرستان: {{ $order->customer->city ?? '' }}</td>
                                    <td>کدپستی: {{$order->customer->zip_code ?? ''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-left">آدرس: <span>{{$order->customer->address ?? ''}} </span></td>
                                    <td>شهر: {{ $order->customer->city ?? '' }}</td>
                                    <td>تلفن: {{$order->customer->mobile ?? ''}}</td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="factortable table table-bordered text-center mt-3" style="font-size: 11px; margin-bottom: 5px;">
                                <thead>
                                <tr class="table-secondary">
                                    <th scope="col">ردیف</th>
                                    <th scope="col">کد کالا</th>
                                    <th scope="col">نام کالا</th>
                                    <th scope="col">تعداد / مقدار</th>
                                    <th scope="col">واحد</th>
                                    <th scope="col">وزن (کیلوگرم)</th>
                                    <th scope="col">تعداد کارتن</th>
                                    <th scope="col">تعداد در کارتن</th>
                                    <th scope="col">مقدار اضافی</th>
                                    <th scope="col">فی</th>
                                    <th scope="col">جمع کل + مالیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->commodity ? $item->commodity->number : '' }}</td>
                                        <td>{{ $item->commodity ? $item->commodity->title : '' }}</td>
                                        <td>{{ number_format($item->commodity_amount) }}</td>
                                        <td>{{ $item->unit_symbol }}</td>
                                        <td>
                                            @if($item->weight_kg !== null)
                                                {{ number_format($item->weight_kg, 3) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($order->box_quantities[$item->commodity_id]) && $order->box_quantities[$item->commodity_id]['can_calculate'])
                                                {{ $order->box_quantities[$item->commodity_id]['boxes'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($order->box_quantities[$item->commodity_id]) && $order->box_quantities[$item->commodity_id]['can_calculate'])
                                                {{ $order->box_quantities[$item->commodity_id]['pieces_per_box'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($order->box_quantities[$item->commodity_id]) && $order->box_quantities[$item->commodity_id]['can_calculate'])
                                                {{ $order->box_quantities[$item->commodity_id]['remaining_pieces'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ number_format($item->price) }}</td>
                                        <td>{{ number_format($item->total_price) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="11" class="text-left" style="vertical-align: top">
                                            <div class="d-flex justify-content-between">
                                                <span>شرایط و نحوه تسویه: </span>
                                                <span>نقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                                <span>غیرنقدی <span class="border" style="display:inline-block;width:15px;height:15px"></span></span>
                                            </div>
                                            <p>توضیحات:</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="11" class="text-left">
                                            <div class="d-flex justify-content-between">
                                                <span>جمع کل : {{ number_format($order->total_price) }}</span>
                                                <span>وزن کل : {{ $order->total_weight_kg !== null ? number_format($order->total_weight_kg, 3) . ' کیلوگرم' : 'نامشخص' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="11" class="text-left">جمع کل به حروف: {{ $order->total_price }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="11" class="text-left" style="height: 80px">مهر و امضای فروشنده:</td>
                                    </tr>
                                    <tr>
                                        <td colspan="11" class="text-left" style="height: 80px">مهر و امضای خریدار:</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
