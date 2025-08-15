@extends('layouts.main')
@section('title', 'لیست سفارشات')
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
        #order-inventory-charts {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .mini-order-chart {
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
            .mini-order-chart {
                flex-basis: 48%;
                min-width: 180px;
                max-width: 100%;
            }
        }
        @media (max-width: 600px) {
            .mini-order-chart {
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
                                         <h4 class="card-title mb-2">نمودار مواد اولیه مورد نیاز (سفارشات در حال پردازش) و موجودی</h4>
                     <p class="text-muted small mb-3">نمودار بر اساس فیلترهای انتخاب شده در لیست سفارشات به‌روزرسانی می‌شود. فقط سفارشات با وضعیت "در حال پردازش" در محاسبات نمودار لحاظ می‌شوند.</p>
                    <div id="order-inventory-charts"></div>
                </div>
            </div>
        </div>
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-2">لیست سفارشات</h4>
                    <!-- Date fields and calculate button in a flex row -->
                    <div id="order-filter-group" class=" justify-content-start gap-2 mb-2" style="width: auto;">
                        <input type="text" id="date_from" class="form-control usage" placeholder="از تاریخ" autocomplete="off" style="min-width: 110px;">
                        <input type="text" id="date_to" class="form-control usage" placeholder="تا تاریخ" autocomplete="off" style="min-width: 110px;">
                        <button id="calculate-orders" class="btn btn-success ml-2" type="button">محاسبه</button>
                        <button id="clear-filters" class="btn btn-outline-secondary ml-2" type="button">پاک کردن فیلترها</button>
                    </div>
                    <div id="filter-status" class="text-info small mb-2" style="display: none;"></div>
                    <table id="datatable-buttons-customer" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all-orders" checked>
                                    <span style="margin-right: 5px;">همه</span>
                                </th>
                                <th>ردیف</th>
                                <th>{{ __('fields.customer') }}</th>
                                <th>مجموع مقدار</th>
                                <th>{{ __('fields.deadline') }}</th>
                                <th>{{ __('fields.status') }}</th>
                                <th>{{ __('fields.creator') }}</th>
                                <th>{{ __('fields.details') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @php($i = 1)
                            @foreach ($orders as $order)
                                <tr data-order-id="{{ $order->id }}">
                                    <td><input type="checkbox" class="order-checkbox"></td>
                                    <td>{{ $i }}</td>
                                    <td>{{ $order->customer ? $order->customer->name : 'مشتری حذف شده' }}</td>
                                    <td>{{ number_format($order->orderItems->sum('commodity_amount')) }}</td>
                                    <td>{{ date('Y/m/d', strtotime($order->deadline)) }}</td>
                                                                            <td>{{ __('fields.order.status.' . $order->status) }}</td>
                                    <td>سیستم</td>
                                    <td><a href="{{ route('order.show', $order) }}" class=""><i class="ti-more-alt font-24"></i></a></td>
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
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/daterange-picker.js') }}"></script>
    <script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/jszip.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/default-assets/buttons.html5.min.js') }}"></script>
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
                            doc.content[1].table.widths = ['20%','20%', '20%', '20%', '20%', '20%'];
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

            // Move the date fields and calculate button next to the DataTable search box
            var $orderFilterGroup = $('#order-filter-group').detach();
            $('#datatable-buttons-customer_filter').addClass('d-flex align-items-center gap-2').append($orderFilterGroup);
            $('#datatable-buttons-customer_filter input[type="search"]').addClass('ml-2');

            $('#select-all-orders').prop('checked', true);
            $('.order-checkbox').prop('checked', false);

            // Remove custom datepicker initialization for date_from and date_to
            // The global $(".usage").persianDatepicker() in bootstrap-datepicker.min.js will handle all .usage fields

            var mockMode = false; // If true, generates mock data

            function getSelectedOrderData() {
                // Convert Persian digits to English digits
                function faToEn(str) {
                    if (!str) return '';
                    return str.replace(/[۰-۹]/g, function (d) {
                        return '۰۱۲۳۴۵۶۷۸۹'.indexOf(d);
                    });
                }
                // Convert date string to number for comparison (YYYY/MM/DD -> YYYYMMDD), always pad month and day
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
                    orderIds: []
                };

                // --- Date range filter ---
                var dateFrom = $('#date_from').val();
                var dateTo = $('#date_to').val();

                var fromNum = toNum(dateFrom);
                var toNumVal = toNum(dateTo);

                // If both date fields are empty, ignore date filtering (show all rows)
                var filterByDate = !!(fromNum || toNumVal);

                // Get checked rows that are currently visible (filtered by date)
                var checkedRows = $('#datatable-buttons-customer tbody tr:visible').filter(function() {
                    var checkbox = $(this).find('.order-checkbox');
                    return checkbox.length && checkbox.is(':checked');
                });
                
                // If no row is checked but 'select all' is checked, include all visible rows
                if (checkedRows.length === 0 && $('#select-all-orders').is(':checked')) {
                    checkedRows = $('#datatable-buttons-customer tbody tr:visible');
                }
                
                // Collect all filtered order_ids for backend use
                checkedRows.each(function() {
                    var $row = $(this);
                    var orderId = $row.data('order-id');
                    data.orderIds.push(orderId);
                });
                
                return data;
            }

            function renderCharts(data) {
                $('#order-inventory-charts').empty();
                if (data.names.length === 0) {
                    $('#order-inventory-charts').append('<div class="alert alert-info text-center">هیچ سفارش در حال پردازشی انتخاب نشده است یا محصولات انتخاب شده فرمول مواد اولیه ندارند.</div>');
                    return;
                }
                data.names.forEach(function(name, idx) {
                    var chartId = 'order-inventory-chart-' + idx;
                    $('#order-inventory-charts').append('<div id="'+chartId+'" class="mini-order-chart"></div>');
                    
                    // Use real inventory data if available, otherwise use 0
                    var inventoryAmount = data.inventory && data.inventory[idx] !== undefined ? data.inventory[idx] : 0;
                    
                    var orderData = {
                        chart: { height: 220, type: "bar" },
                        plotOptions: { bar: { horizontal: false, columnWidth: "55%", endingShape: "rounded" } },
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ["transparent"] },
                        series: [
                            { name: "مواد اولیه مورد نیاز", data: [data.amounts[idx]] },
                            { name: "موجودی مواد اولیه", data: [inventoryAmount] }
                        ],
                        colors: [ "#1976d2","#e53935"],
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
                    var chart = new ApexCharts(document.getElementById(chartId), orderData);
                    chart.render();
                });
            }

            // Initial chart rendering with all orders (since 'select all' is checked and no date filter)
            updateChart();

            // Function to update chart based on current filters
            function updateChart() {
                var selectedData = getSelectedOrderData();

                // Use real data from the backend
                $.ajax({
                    url: '{{ route("order.chart.data") }}',
                    method: 'POST',
                    data: {
                        order_ids: selectedData.orderIds,
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        renderCharts({
                            names: response.names,
                            amounts: response.amounts,
                            units: response.units,
                            inventory: response.inventory
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching chart data:', xhr);
                        console.error('Status:', status);
                        console.error('Error:', error);
                        console.error('Response Text:', xhr.responseText);
                        // Show error message if AJAX fails
                        $('#order-inventory-charts').empty().append('<div class="alert alert-danger text-center">خطا در دریافت اطلاعات نمودار<br><small>Status: ' + status + '<br>Error: ' + error + '</small></div>');
                    }
                });
            }

            // Calculate button click handler
            $('#calculate-orders').on('click', function() {
                filterTableByDate();
                updateChart();
            });
            
            // Clear filters button click handler
            $('#clear-filters').on('click', function() {
                $('#date_from').val('');
                $('#date_to').val('');
                filterTableByDate();
                updateChart();
            });

            // Select all functionality for order checkboxes
            $('#select-all-orders').on('change', function() {
                var checked = $(this).is(':checked');
                // Only check visible rows (filtered by date)
                $('.order-checkbox:visible').prop('checked', checked);
                // Update chart automatically when select all changes
                updateChart();
            });
            
            $(document).on('change', '.order-checkbox', function() {
                var visibleCheckboxes = $('.order-checkbox:visible');
                var checkedVisibleCheckboxes = visibleCheckboxes.filter(':checked');
                $('#select-all-orders').prop('checked', visibleCheckboxes.length > 0 && checkedVisibleCheckboxes.length === visibleCheckboxes.length);
                // Update chart automatically when individual checkboxes change
                updateChart();
            });

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
                
                $('#datatable-buttons-customer tbody tr').each(function() {
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
                var visibleRows = $('#datatable-buttons-customer tbody tr:visible');
                var checkedVisibleRows = visibleRows.find('.order-checkbox:checked');
                $('#select-all-orders').prop('checked', visibleRows.length > 0 && checkedVisibleRows.length === visibleRows.length);
            }
            
            // Update chart when date filters change (with debounce to prevent too many calls)
            var dateUpdateTimeout;
            $('#date_from, #date_to').on('change', function() {
                clearTimeout(dateUpdateTimeout);
                dateUpdateTimeout = setTimeout(function() {
                    filterTableByDate();
                    updateChart();
                }, 500); // 500ms delay
            });
        });
    </script>
@endsection