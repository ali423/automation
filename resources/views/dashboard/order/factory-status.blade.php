@extends('layouts.main')
@section('title', 'وضعیت کارخانه')

@section('page_styles')
    <!-- These plugins only need for the run this page -->
    <link rel="stylesheet" href="{{ asset('css/default-assets/datatables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/select.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables-td.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/daterange-picker.css') }}">
    <style>
        #factory-charts {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .mini-factory-chart {
            flex: 1 1 300px;
            min-width: 240px;
            max-width: 350px;
            height: 260px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            padding: 12px;
            margin-bottom: 0;
            box-sizing: border-box;
        }
        @media (max-width: 900px) {
            .mini-factory-chart {
                flex-basis: 48%;
                min-width: 180px;
                max-width: 100%;
            }
        }
        @media (max-width: 600px) {
            .mini-factory-chart {
                flex-basis: 100%;
                min-width: 120px;
                max-width: 100%;
            }
        }
        .summary-card {
            transition: transform 0.2s ease-in-out;
            margin-bottom: 15px;
        }
        .summary-card:hover {
            transform: translateY(-2px);
        }
        .summary-card .card-body {
            padding: 15px;
        }
        .summary-card h6 {
            font-size: 0.8rem;
            margin-bottom: 8px;
            opacity: 0.9;
        }
        .summary-card h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        @media (max-width: 768px) {
            .summary-card h4 {
                font-size: 1.2rem;
            }
            .summary-card h6 {
                font-size: 0.7rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-2">نمودار وضعیت کارخانه</h4>
                    <div id="factory-charts"></div>
                </div>
            </div>
        </div>
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-2">ارزیابی تحویل سفارشات بر اساس موجودی انبار</h4>
                    
                    <!-- Orders Summary -->
                    @include('dashboard.order.partials.order-summary-stats', ['summaryStats' => $summaryStats])
                    
                    {{-- Pagination Controls (match index/chart pattern; no date range) --}}
                    <x-pagination-controls :paginator="$pendingOrders" :options="$options" />
                    <div id="filter-status" class="text-info small mb-2" style="display: none;"></div>
                    <table id="datatable-buttons-factory" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all-factory" checked>
                                    <span style="margin-right: 5px;">همه</span>
                                </th>
                                <th>ردیف</th>
                                <th>خریدار</th>

                                <th>تاریخ تحویل</th>
                                <th>وضعیت تحویل</th>
                                <th>جزئیات</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @if($pendingOrders->count() > 0)
                                @php($i = ($pendingOrders->currentPage() - 1) * $pendingOrders->perPage() + 1)
                                @foreach ($pendingOrders as $order)
                                    <tr data-order-id="{{ $order->id }}" 
                                        class="@if($order->can_deliver) table-success @else table-danger @endif">
                                        <td><input type="checkbox" class="factory-checkbox" checked></td>
                                        <td>{{ $i }}</td>
                                        <td>{{ $order->customer->name ?? 'نامشخص' }}</td>

                                        <td>{{ $order->deadline ?? 'نامشخص' }}</td>
                                        <td>
                                            <span class="badge @if($order->can_deliver) bg-success @else bg-danger @endif">
                                                @if($order->can_deliver)
                                                    قابل تحویل
                                                @else
                                                    غیرقابل تحویل
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('order.factory-status.customer', $order->customer->id ?? 0) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ti-more-alt"></i> جزئیات
                                            </a>
                                        </td>
                                    </tr>
                                    @php($i++)
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">هیچ سفارش معلقی یافت نشد.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <x-pagination-navigation :paginator="$pendingOrders" />
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/daterange-picker.js') }}"></script>
    <script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/jszip.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.print.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.sorting.persian.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

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

            $('#datatable-buttons-factory').DataTable({
                dom: 'Bfrtip',
                paging: false,
                searching: false,
                info: false,
                buttons: [                    {
                        extend: 'copy',
                        text: "کپی",
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [6, 5, 4, 3, 2, 1, 0],
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
                            columns: [6, 5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            },
                            orthogonal: "rtlexport"
                        },
                        customize: function(doc) {
                            doc.defaultStyle.font = "IRANSansWeb";
                            doc.content[1].table.widths = ['14%','14%', '14%', '14%', '14%', '14%', '16%'];
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [6, 5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [6, 5, 4, 3, 2, 1, 0],
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
                            columns: [0, 1, 2, 3, 4, 5, 6],
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

            $('#select-all-factory').prop('checked', true);
            $('.factory-checkbox').prop('checked', false);

            // Load full dataset from backend, but render based on current visible/selected rows
            var factoryData = @json($warehouseChartData['orders']);
            updateCharts();

            // No client-side date filtering in factory view; server-side filters via shared controls

            // Function to update charts based on current filters
            function updateCharts() {
                var selectedData = getSelectedFactoryData();
                
                if (selectedData.names.length === 0) {
                    var message = 'هیچ سفارشی انتخاب نشده است.';
                    if ($('#date_from').val() || $('#date_to').val()) {
                        message += ' (ممکن است فیلتر تاریخ باعث شده باشد هیچ ردیفی نمایش داده نشود)';
                    }
                    $('#factory-charts').html('<div class="alert alert-warning text-center">' + message + '</div>');
                } else {
                    renderFactoryCharts({
                        names: selectedData.names,
                        inventory: selectedData.inventory,
                        orders: selectedData.orders,
                        units: selectedData.units
                    });
                }
            }

            function getSelectedFactoryData() {
                var data = {
                    names: [],
                    inventory: [],
                    orders: [],
                    units: []
                };

                // Get checked rows from visible (filtered) rows
                var checkedRows = $('#datatable-buttons-factory tbody tr:visible').filter(function() {
                    var checkbox = $(this).find('.factory-checkbox');
                    return checkbox.length && checkbox.is(':checked');
                });

                // If no row is checked but 'select all' is checked, include all visible rows
                if (checkedRows.length === 0 && $('#select-all-factory').is(':checked')) {
                    checkedRows = $('#datatable-buttons-factory tbody tr:visible');
                }

                // Get real data from selected rows
                if (checkedRows.length > 0) {
                    checkedRows.each(function(index) {
                        var $row = $(this);
                        var rowId = $row.data('order-id');
                        
                        // Find the corresponding order data from the initial factory data
                        var orderData = factoryData.find(function(item) {
                            return item.orderId == rowId;
                        });
                        
                        if (orderData) {
                            data.names.push(orderData.productName);
                            data.inventory.push(orderData.inventory);
                            data.orders.push(orderData.orderedAmount);
                            data.units.push(orderData.unitSymbol || orderData.unit);
                        }
                    });
                }

                return data;
            }

            // Calculate button click handler
            $('#calculate-factory').on('click', function() {
                filterTableByDate();
                updateCharts();
            });

            // Select all functionality for factory checkboxes
            $('#select-all-factory').on('change', function() {
                var checked = $(this).is(':checked');
                // Only check visible rows (filtered by date)
                $('.factory-checkbox:visible').prop('checked', checked);
                // Update chart automatically when select all changes
                updateCharts();
            });
            
            $(document).on('change', '.factory-checkbox', function() {
                var visibleCheckboxes = $('.factory-checkbox:visible');
                var checkedVisibleCheckboxes = visibleCheckboxes.filter(':checked');
                $('#select-all-factory').prop('checked', visibleCheckboxes.length > 0 && checkedVisibleCheckboxes.length === visibleCheckboxes.length);
                // Update chart automatically when individual checkboxes change
                updateCharts();
            });
        });

        function renderFactoryCharts(data) {
            $('#factory-charts').empty();
            if (data.names.length === 0) {
                $('#factory-charts').append('<div class="alert alert-info text-center">هیچ داده‌ای موجود نیست.</div>');
                return;
            }
            data.names.forEach(function(name, idx) {
                var chartId = 'factory-chart-' + idx;
                $('#factory-charts').append('<div id="'+chartId+'" class="mini-factory-chart"></div>');
                var factoryData = {
                    chart: { height: 220, type: "bar" },
                    plotOptions: { bar: { horizontal: false, columnWidth: "55%", endingShape: "rounded" } },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ["transparent"] },
                    series: [
                        { name: "موجودی انبار", data: [data.inventory[idx]] },
                        { name: "سفارشات", data: [data.orders[idx]] }
                    ],
                    colors: ["#007bff", "#dc3545"],
                    xaxis: { categories: [name] },
                    yaxis: { title: { text: data.units[idx] } },
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            formatter: function (e) {
                                return e + ' ' + data.units[idx];
                            },
                        },
                    },
                };
                var chart = new ApexCharts(document.getElementById(chartId), factoryData);
                chart.render();
            });
        }
    </script>
@endsection
