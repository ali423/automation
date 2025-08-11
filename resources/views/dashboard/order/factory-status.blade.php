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
                    <!-- Date fields and calculate button in a flex row -->
                    <div id="factory-filter-group" class=" justify-content-start gap-2 mb-2" style="width: auto;">
                        <input type="text" id="date_from" class="form-control usage" placeholder="از تاریخ" autocomplete="off" style="min-width: 110px;">
                        <input type="text" id="date_to" class="form-control usage" placeholder="تا تاریخ" autocomplete="off" style="min-width: 110px;">
                        <button id="calculate-factory" class="btn btn-success ml-2" type="button">محاسبه</button>
                    </div>
                    <table id="datatable-buttons-factory" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all-factory" checked>
                                    <span style="margin-right: 5px;">همه</span>
                                </th>
                                <th>ردیف</th>
                                <th>خریدار</th>
                                <th>مشخصات خریدار</th>
                                <th>تاریخ تحویل</th>
                                <th>وضعیت تحویل</th>
                                <th>جزئیات</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @php
                                // Mock data for customers with delivery evaluation
                                $customers = collect([
                                    (object)[
                                        'id' => 1,
                                        'name' => 'شرکت آلفا',
                                        'details' => 'صنایع خودروسازی - تهران',
                                        'delivery_date' => '1403/02/15',
                                        'total_orders' => 15000,
                                        'inventory_available' => 18000,
                                        'can_deliver' => true,
                                        'delivery_status' => 'قابل تحویل'
                                    ],
                                    (object)[
                                        'id' => 2,
                                        'name' => 'کارخانه بتا',
                                        'details' => 'صنایع پتروشیمی - اصفهان',
                                        'delivery_date' => '1403/02/20',
                                        'total_orders' => 8000,
                                        'inventory_available' => 6000,
                                        'can_deliver' => false,
                                        'delivery_status' => 'غیرقابل تحویل'
                                    ],
                                    (object)[
                                        'id' => 3,
                                        'name' => 'شرکت گاما',
                                        'details' => 'صنایع فولاد - کرج',
                                        'delivery_date' => '1403/02/18',
                                        'total_orders' => 12000,
                                        'inventory_available' => 12000,
                                        'can_deliver' => true,
                                        'delivery_status' => 'قابل تحویل'
                                    ],
                                    (object)[
                                        'id' => 4,
                                        'name' => 'صنایع دلتا',
                                        'details' => 'صنایع غذایی - شیراز',
                                        'delivery_date' => '1403/02/25',
                                        'total_orders' => 25000,
                                        'inventory_available' => 15000,
                                        'can_deliver' => false,
                                        'delivery_status' => 'غیرقابل تحویل'
                                    ],
                                    (object)[
                                        'id' => 5,
                                        'name' => 'شرکت زتا',
                                        'details' => 'صنایع دارویی - مشهد',
                                        'delivery_date' => '1403/02/12',
                                        'total_orders' => 5000,
                                        'inventory_available' => 8000,
                                        'can_deliver' => true,
                                        'delivery_status' => 'قابل تحویل'
                                    ]
                                ]);
                            @endphp
                            @php($i = 1)
                            @foreach ($customers as $customer)
                                <tr data-customer-id="{{ $customer->id }}" 
                                    class="@if($customer->can_deliver) table-success @else table-danger @endif">
                                    <td><input type="checkbox" class="factory-checkbox"></td>
                                    <td>{{ $i }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->details }}</td>
                                    <td>{{ $customer->delivery_date }}</td>
                                    <td>
                                        <span class="badge @if($customer->can_deliver) bg-success @else bg-danger @endif">
                                            {{ $customer->delivery_status }}
                                        </span>
                                    </td>
                                    <td><a href="{{ route('order.factory-status.customer', $customer->id) }}" class=""><i class="ti-more-alt font-24"></i></a></td>
                                </tr>
                                @php($i++)
                                @endforeach
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

            var mockMode = false; // If true, generates mock data

            function getSelectedFactoryData() {
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
                
                var data = {
                    names: [],
                    inventory: [],
                    orders: [],
                    units: []
                };

                // Get date filter values
                var dateFrom = $('#date_from').val();
                var dateTo = $('#date_to').val();
                var fromNum = toNum(dateFrom);
                var toNumVal = toNum(dateTo);

                // Filter rows by date range
                var dateFilteredRows = $('#datatable-buttons-factory tbody tr').filter(function() {
                    var dateStr = $(this).find('td').eq(4).text().trim(); // Delivery date column
                    var dateNum = toNum(dateStr);
                    
                    // If no date filter is applied, show all rows
                    if (!fromNum && !toNumVal) return true;
                    
                    var inRange = true;
                    if (fromNum && dateNum < fromNum) inRange = false;
                    if (toNumVal && dateNum > toNumVal) inRange = false;
                    return inRange;
                });

                // Get checked rows from date-filtered rows
                var checkedRows = dateFilteredRows.filter(function() {
                    var checkbox = $(this).find('.factory-checkbox');
                    return checkbox.length && checkbox.is(':checked');
                });

                // If no row is checked but 'select all' is checked, include all date-filtered rows
                if (checkedRows.length === 0 && $('#select-all-factory').is(':checked')) {
                    checkedRows = dateFilteredRows;
                }

                // Generate random chart data based on selected rows
                if (checkedRows.length > 0) {
                    checkedRows.each(function(index) {
                        var $row = $(this);
                        var rowId = $row.data('customer-id');
                        
                        // Generate random data based on row ID for consistency
                        var seed = rowId || (index + 1);
                        var randomInventory = 5000 + (seed * 1234) % 20000;
                        var randomOrders = 3000 + (seed * 567) % 15000;
                        
                        // Different units based on row ID
                        var units = ['لیتر', 'کیلوگرم', 'تن', 'متر مکعب'];
                        var unit = units[seed % units.length];
                        
                        // Product names based on row ID
                        var productNames = ['روغن موتور', 'گریس صنعتی', 'روغن هیدرولیک', 'روغن دنده', 'روغن ترمز'];
                        var productName = productNames[seed % productNames.length];
                        
                        data.names.push(productName);
                        data.inventory.push(randomInventory);
                        data.orders.push(randomOrders);
                        data.units.push(unit);
                    });
                }

                return data;
            }

            // Initial chart data - show all data at first
            var factoryData = {
                names: ['روغن موتور', 'گریس صنعتی', 'روغن هیدرولیک', 'روغن دنده', 'روغن ترمز'],
                inventory: [18000, 12000, 15000, 8000, 10000],
                orders: [15000, 8000, 12000, 6000, 9000],
                units: ['لیتر', 'کیلوگرم', 'لیتر', 'لیتر', 'لیتر']
            };

            renderFactoryCharts({
                names: factoryData.names,
                inventory: factoryData.inventory,
                orders: factoryData.orders,
                units: factoryData.units
            });

            // Calculate button click handler
            $('#calculate-factory').on('click', function() {
                var selectedData = getSelectedFactoryData();
                
                // Show loading message
                $('#factory-charts').html('<div class="alert alert-info text-center">در حال محاسبه نمودارها...</div>');
                
                // Small delay to show loading message
                setTimeout(function() {
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
                        
                        // Show success message
                        var totalOrders = selectedData.names.length;
                        var message = 'نمودارها بر اساس ' + totalOrders + ' سفارش انتخاب شده به‌روزرسانی شدند.';
                        if ($('#date_from').val() || $('#date_to').val()) {
                            message += ' (فیلتر تاریخ اعمال شده)';
                        }
                        
                        // You can add a toast notification here if you have a notification system
                        console.log(message);
                    }
                }, 500);
            });

            // Select all functionality for factory checkboxes
            $('#select-all-factory').on('change', function() {
                var checked = $(this).is(':checked');
                $('.factory-checkbox').prop('checked', checked);
            });
            
            $(document).on('change', '.factory-checkbox', function() {
                var total = $('.factory-checkbox').length;
                var checked = $('.factory-checkbox:checked').length;
                $('#select-all-factory').prop('checked', total === checked);
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
