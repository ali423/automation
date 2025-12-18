@extends('layouts.main')
@section('title', 'جزئیات خریدار - ' . $customer->name)

@section('page_styles')
    <!-- These plugins only need for the run this page -->
    <link rel="stylesheet" href="{{ asset('css/default-assets/datatables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/select.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables-td.css') }}">
    <style>
        .customer-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .customer-info h3 {
            margin-bottom: 15px;
            font-weight: 600;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: 600;
            margin-left: 10px;
        }
        .summary-card {
            transition: transform 0.2s ease-in-out;
            margin-bottom: 15px;
        }
        .summary-card:hover {
            transform: translateY(-5px);
        }
        .summary-card .card-body {
            padding: 15px;
        }
        .summary-card h5 {
            font-size: 0.9rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .summary-card h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        @media (max-width: 768px) {
            .summary-card h3 {
                font-size: 1.4rem;
            }
            .summary-card h5 {
                font-size: 0.8rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">جزئیات خریدار</h4>
                        <a href="{{ route('order.factory-status') }}" class="btn btn-outline-primary">
                            <i class="ti-arrow-right"></i> بازگشت به لیست
                        </a>
                    </div>
                    
                    <!-- Customer Information -->
                    <div class="customer-info">
                        <h3><i class="ti-user"></i> {{ $customer->name }}</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="ti-briefcase"></i>
                                    <span class="info-label">نوع کسب‌وکار:</span>
                                    {{ $customer->comp_name ?? 'نامشخص' }}
                                </div>
                                <div class="info-item">
                                    <i class="ti-mobile"></i>
                                    <span class="info-label">تلفن:</span>
                                    {{ $customer->phone ?? $customer->mobile ?? 'نامشخص' }}
                                </div>
                                <div class="info-item">
                                    <i class="ti-id-badge"></i>
                                    <span class="info-label">شماره اقتصادی:</span>
                                    {{ $customer->economic_code ?? 'نامشخص' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="ti-email"></i>
                                    <span class="info-label">ایمیل:</span>
                                    {{ $customer->email ?? 'نامشخص' }}
                                </div>
                                <div class="info-item">
                                    <i class="ti-location-pin"></i>
                                    <span class="info-label">آدرس:</span>
                                    {{ $customer->address ?? 'نامشخص' }}
                                </div>
                                <div class="info-item">
                                    <i class="ti-map-pin"></i>
                                    <span class="info-label">کد پستی:</span>
                                    {{ $customer->zip_code ?? 'نامشخص' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    @if(isset($orderId) && $orderId)
                        <h4 class="card-title mb-2">جزئیات سفارش #{{ $orderId }} - {{ $customer->name }}</h4>
                        <p class="text-muted mb-3"><small>نمایش اقلام سفارش شماره {{ $orderId }}</small></p>
                    @else
                        <h4 class="card-title mb-2">سفارشات معلق {{ $customer->name }}</h4>
                        <p class="text-muted mb-3"><small>این صفحه فقط سفارشات با وضعیت "معلق" را نمایش می‌دهد</small></p>
                    @endif
                    
                    <!-- Orders Summary -->
                    @include('dashboard.order.partials.order-summary-stats', ['summaryStats' => $summaryStats])
                    
                    <table id="datatable-buttons-customer" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ردیف</th>
                                <th>شماره سفارش</th>
                                <th>نام کالا</th>
                                <th>مقدار سفارش</th>
                                <th>واحد</th>
                                <th>موجودی انبار</th>
                                <th>مهلت تحویل</th>
                                <th>وضعیت تحویل</th>
                                <th>مبلغ کل</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @if($orders->count() > 0)
                                @php($i = 1)
                                @foreach ($orders as $order)
                                    <tr class="@if($order->can_deliver) table-success @else table-danger @endif">
                                        <td>{{ $i }}</td>
                                        <td><strong>#{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->commodity_title }}</td>
                                        <td>{{ number_format($order->amount) }} {{ $order->unit_symbol }}</td>
                                        <td>{{ $order->unit }}</td>
                                        <td>{{ number_format($order->inventory) }} {{ $order->unit_symbol }}</td>
                                        <td>{{ $order->deadline }}</td>
                                        <td>
                                            <span class="badge @if($order->can_deliver) bg-success @else bg-danger @endif">
                                                @if($order->can_deliver)
                                                    قابل تحویل
                                                @else
                                                    غیر قابل تحویل
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ number_format($order->total_value) }} ریال</td>
                                    </tr>
                                    @php($i++)
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center">هیچ سفارش معلقی برای این مشتری یافت نشد.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/jszip.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.print.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.sorting.persian.js') }}"></script>

    <script>
        $(document).ready(function() {
            pdfMake.fonts = {
                Roboto: {
                    normal: 'Roboto-Regular.ttf',
                    bold: 'Roboto-Medium.ttf',
                    italics: 'Roboto-Italic.ttf',
                    bolditalics: 'Roboto-MediumItalic.ttf'
                },
                IRANSansWeb: {
                    normal: "IRANSansWeb400.ttf",
                    bold: "IRANSansWeb400.ttf",
                    italics: "IRANSansWeb400.ttf",
                    bolditalics: "IRANSansWeb400.ttf"
                }
            };

            $('#datatable-buttons-customer').DataTable({
                dom: 'Bfrtip',
                buttons: [                    {
                        extend: 'copy',
                        text: "کپی",
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [8, 7, 6, 5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            },
                            orthogonal: "rtlexport"
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'pdf',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [8, 7, 6, 5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            },
                            orthogonal: "rtlexport"
                        },
                        customize: function(doc) {
                            doc.defaultStyle.font = "IRANSansWeb";
                            doc.content[1].table.widths = ['8%','10%', '14%', '10%', '8%', '10%', '10%', '12%', '18%'];
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [7, 6, 5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [7, 6, 5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                    {
                        extend: 'print',
                        text: "پرینت",
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                            modifier: {
                                page: 'current'
                            },
                            orthogonal: "rtlexport"
                        }
                    }
                ],
                columnDefs: [{
                    targets: '_all',
                    render: function(data, type, row) {
                        if (type === 'rtlexport') {
                            return data.split(' ').reverse().join(' ');
                        }
                        return data;
                    }
                }],
                "language": {
                    "paginate": {
                        "previous": "قبلی",
                        "next": "بعدی"
                    }
                }
            });
        });
    </script>
@endsection
