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
                                    {{ $customer->details }}
                                </div>
                                <div class="info-item">
                                    <i class="ti-mobile"></i>
                                    <span class="info-label">تلفن:</span>
                                    {{ $customer->phone }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="ti-email"></i>
                                    <span class="info-label">ایمیل:</span>
                                    {{ $customer->email }}
                                </div>
                                <div class="info-item">
                                    <i class="ti-location-pin"></i>
                                    <span class="info-label">آدرس:</span>
                                    {{ $customer->address }}
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
                    <h4 class="card-title mb-2">لیست سفارشات {{ $customer->name }}</h4>
                    <table id="datatable-buttons-customer" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ردیف</th>
                                <th>شماره سفارش</th>
                                <th>نام کالا</th>
                                <th>مقدار</th>
                                <th>واحد</th>
                                <th>مهلت تحویل</th>
                                <th>موجودی فرآورده</th>
                                <th>مبلغ کل</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @php
                                // Mock data for orders with delivery evaluation
                                $orders = collect([
                                    (object)[
                                        'id' => 1,
                                        'order_number' => 'ORD-001',
                                        'commodity_title' => 'روغن موتور',
                                        'amount' => 5000,
                                        'unit' => 'لیتر',
                                        'deadline' => '1403/02/15',
                                        'inventory' => 8000,
                                        'total_value' => 25000000,
                                        'can_deliver' => true
                                    ],
                                    (object)[
                                        'id' => 2,
                                        'order_number' => 'ORD-002',
                                        'commodity_title' => 'گریس صنعتی',
                                        'amount' => 3000,
                                        'unit' => 'کیلوگرم',
                                        'deadline' => '1403/02/20',
                                        'inventory' => 2000,
                                        'total_value' => 15000000,
                                        'can_deliver' => false
                                    ],
                                    (object)[
                                        'id' => 3,
                                        'order_number' => 'ORD-003',
                                        'commodity_title' => 'روغن هیدرولیک',
                                        'amount' => 4000,
                                        'unit' => 'لیتر',
                                        'deadline' => '1403/02/18',
                                        'inventory' => 6000,
                                        'total_value' => 20000000,
                                        'can_deliver' => true
                                    ],
                                    (object)[
                                        'id' => 4,
                                        'order_number' => 'ORD-004',
                                        'commodity_title' => 'روغن دنده',
                                        'amount' => 2000,
                                        'unit' => 'لیتر',
                                        'deadline' => '1403/02/25',
                                        'inventory' => 1500,
                                        'total_value' => 12000000,
                                        'can_deliver' => false
                                    ],
                                    (object)[
                                        'id' => 5,
                                        'order_number' => 'ORD-005',
                                        'commodity_title' => 'روغن ترمز',
                                        'amount' => 1500,
                                        'unit' => 'لیتر',
                                        'deadline' => '1403/02/12',
                                        'inventory' => 3000,
                                        'total_value' => 8000000,
                                        'can_deliver' => true
                                    ]
                                ]);
                            @endphp
                            @php($i = 1)
                            @foreach ($orders as $order)
                                <tr class="@if($order->can_deliver) table-success @else table-danger @endif">
                                    <td>{{ $i }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->commodity_title }}</td>
                                    <td>{{ number_format($order->amount) }}</td>
                                    <td>{{ $order->unit }}</td>
                                    <td>{{ $order->deadline }}</td>
                                    <td>{{ number_format($order->inventory) }}</td>
                                    <td>{{ number_format($order->total_value) }} ریال</td>
                                </tr>
                                @php($i++)
                            @endforeach
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
                            columns: [7, 6, 5, 4, 3, 2, 1, 0],
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
                            columns: [7, 6, 5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            },
                            orthogonal: "rtlexport"
                        },
                        customize: function(doc) {
                            doc.defaultStyle.font = "IRANSansWeb";
                            doc.content[1].table.widths = ['12%','12%', '12%', '12%', '12%', '12%', '12%', '16%'];
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
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
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
