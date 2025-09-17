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
                    @if($summaryStats['totalOrders'] > 0)
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <div class="card bg-primary text-white text-center summary-card">
                                    <div class="card-body">
                                        <h6>کل سفارشات</h6>
                                        <h4>{{ $summaryStats['totalOrders'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card bg-success text-white text-center summary-card">
                                    <div class="card-body">
                                        <h6>قابل تحویل</h6>
                                        <h4>{{ $summaryStats['canDeliverCount'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card bg-danger text-white text-center summary-card">
                                    <div class="card-body">
                                        <h6>غیرقابل تحویل</h6>
                                        <h4>{{ $summaryStats['cannotDeliverCount'] }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card bg-info text-white text-center summary-card">
                                    <div class="card-body">
                                        <h6>ارزش کل</h6>
                                        <h6>{{ number_format($summaryStats['totalValue']) }} ریال</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card bg-warning text-white text-center summary-card">
                                    <div class="card-body">
                                        <h6>مقدار سفارش</h6>
                                        <h6>{{ number_format($summaryStats['totalAmount']) }}</h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                    
                    <!-- Date fields and calculate button in a flex row -->
                    <div id="factory-filter-group" class=" justify-content-start gap-2 mb-2" style="width: auto;">
                        <input type="text" id="date_from" class="form-control usage" placeholder="از تاریخ" autocomplete="off" style="min-width: 110px;">
                        <input type="text" id="date_to" class="form-control usage" placeholder="تا تاریخ" autocomplete="off" style="min-width: 110px;">
                        <button id="calculate-factory" class="btn btn-success ml-2" type="button">محاسبه</button>
                        <button id="clear-filters" class="btn btn-outline-secondary ml-2" type="button">پاک کردن فیلترها</button>
                    </div>
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
                                @php($i = 1)
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

            // Move the date fields and calculate button next to the DataTable search box
            var $factoryFilterGroup = $('#factory-filter-group').detach();
            $('#datatable-buttons-factory_filter').addClass('d-flex align-items-center gap-2').append($factoryFilterGroup);
            $('#datatable-buttons-factory_filter input[type="search"]').addClass('ml-2');

            $('#select-all-factory').prop('checked', true);
            $('.factory-checkbox').prop('checked', false);

            // Initial chart data - use real data from controller
            var factoryData = @json($warehouseChartData['orders']);
            
            if (factoryData.length > 0) {
                var chartData = {
                    names: factoryData.map(function(item) { return item.productName; }),
                    inventory: factoryData.map(function(item) { return item.inventory; }),
                    orders: factoryData.map(function(item) { return item.orderedAmount; }),
                    units: factoryData.map(function(item) { return item.unitSymbol || item.unit; })
                };
                
                renderFactoryCharts(chartData);
            } else {
                $('#factory-charts').html('<div class="alert alert-info text-center">هیچ سفارش معلقی برای نمایش نمودار وجود ندارد.</div>');
            }

            // Function to filter table rows based on date range
            function filterTableByDate() {
                // Convert Persian digits to English digits
                function faToEn(str) {
                    if (!str) return '';
                    return str.replace(/[۰-۹]/g, function (d) {
                        return '۰۱۲۳۴۵۶۷۸۹'.indexOf(d);
                    });
                }
                // Convert date string to number for comparison (YYYY/MM/DD -> YYYYMMDD)
                function toNum(str) {
                    if (!str) return null;
                    str = faToEn(str);
                    var parts = str.split('/');
                    if (parts.length !== 3) return null;
                    var y = parts[0];
                    var m = parts[1].length === 1 ? '0' + parts[1] : parts[1];
                    var d = parts[2].length === 1 ? '0' + parts[2] : parts[2];
                    return parseInt(y + m + d);
                }
                
                var dateFrom = $('#date_from').val();
                var dateTo = $('#date_to').val();
                
                var fromNum = toNum(dateFrom);
                var toNumVal = toNum(dateTo);
                
                // If both date fields are empty, show all rows
                var filterByDate = !!(fromNum || toNumVal);
                
                // Update filter status indicator
                if (filterByDate) {
                    var filterText = '';
                    if (dateFrom && dateTo) {
                        filterText = 'فیلتر: از ' + dateFrom + ' تا ' + dateTo;
                    } else if (dateFrom) {
                        filterText = 'فیلتر: از ' + dateFrom;
                    } else if (dateTo) {
                        filterText = 'فیلتر: تا ' + dateTo;
                    }
                    $('#filter-status').text(filterText).show();
                } else {
                    $('#filter-status').hide();
                }
                
                $('#datatable-buttons-factory tbody tr').each(function() {
                    var $row = $(this);
                    var dateStr = $row.find('td').eq(4).text().trim(); // Deadline column
                    var dateNum = toNum(dateStr);
                    
                    if (!filterByDate) {
                        $row.show();
                        return;
                    }
                    
                    // Check if date is within range
                    var inRange = true;
                    if (fromNum && dateNum < fromNum) inRange = false;
                    if (toNumVal && dateNum > toNumVal) inRange = false;
                    
                    if (inRange) {
                        $row.show();
                    } else {
                        $row.hide();
                    }
                });
                
                // Update select all checkbox state
                var visibleRows = $('#datatable-buttons-factory tbody tr:visible');
                var checkedVisibleRows = visibleRows.find('.factory-checkbox:checked');
                $('#select-all-factory').prop('checked', visibleRows.length > 0 && checkedVisibleRows.length === visibleRows.length);
            }

            // Clear filters button click handler
            $('#clear-filters').on('click', function() {
                $('#date_from').val('');
                $('#date_to').val('');
                filterTableByDate();
                updateCharts();
            });

            // Update charts when date filters change (with debounce to prevent too many calls)
            var dateUpdateTimeout;
            $('#date_from, #date_to').on('change', function() {
                clearTimeout(dateUpdateTimeout);
                dateUpdateTimeout = setTimeout(function() {
                    filterTableByDate();
                    updateCharts();
                }, 500); // 500ms delay
            });

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
