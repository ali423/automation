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
                    <h4 class="card-title mb-2">نمودار موجودی سفارشات</h4>
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
                    </div>
                    <table id="datatable-buttons-customer" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all-orders" checked>
                                    <span style="margin-right: 5px;">همه</span>
                                </th>
                                <th>ردیف</th>
                                <th>{{ __('fields.customer') }}</th>
                                <th>{{ __('fields.commodity.name') }}</th>
                                <th>{{ __('fields.commodity.amount') }}</th>
                                <th>{{ __('fields.unit') }}</th>
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
                                    <td>{{ $order->commodity ? $order->commodity->title : 'کالا حذف شده' }}</td>
                                    <td>{{ number_format($order->commodity_amount) }}</td>
                                    <td>{{ __('fields.commodity.units')[$order->unit] }}</td>
                                    <td>{{ date('Y/m/d', strtotime($order->deadline)) }}</td>
                                    <td>{{ __('fields.order.status')[$order->status] }}</td>
                                    @if(isset($order->creator_user))
                                        <td>{{ $order->creator_user->full_name }}</td>
                                    @else
                                        <td>سیستم</td>
                                    @endif
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
                buttons: [{
                        extend: 'copy',
                        text: "کپی",
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [5, 4, 3, 2, 1, 0],
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
                            columns: [5, 4, 3, 2, 1, 0],
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
                            columns: [5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [5, 4, 3, 2, 1, 0],
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
                            columns: [0, 1, 2, 3, 4, 5],
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
                    orderIds: [],
                    names: [],
                    amounts: [],
                    units: []
                };
                var commodityMap = {};
                var unitMap = {};

                // --- Date range filter ---
                var dateFrom = $('#date_from').val();
                var dateTo = $('#date_to').val();

                var fromNum = toNum(dateFrom);
                var toNumVal = toNum(dateTo);

                // If both date fields are empty, ignore date filtering (show all rows)
                var filterByDate = !!(fromNum || toNumVal);

                // Filter checked rows by date range if needed
                var checkedRows = $('#datatable-buttons-customer tbody tr').filter(function() {
                    var checkbox = $(this).find('.order-checkbox');
                    var dateStr = $(this).find('td').eq(6).text().trim();
                    var dateNum = toNum(dateStr); // faToEn applied to table value too
                    if (!filterByDate) return checkbox.length && checkbox.is(':checked');
                    // Only keep rows within the selected date range
                    var inRange = true;
                    if (fromNum && dateNum < fromNum) inRange = false;
                    if (toNumVal && dateNum > toNumVal) inRange = false;
                    return checkbox.length && checkbox.is(':checked') && inRange;
                });
                // If no row is checked but 'select all' is checked, include all rows in range
                if (checkedRows.length === 0 && $('#select-all-orders').is(':checked')) {
                    checkedRows = $('#datatable-buttons-customer tbody tr').filter(function() {
                        var dateStr = $(this).find('td').eq(6).text().trim();
                        var dateNum = toNum(dateStr);
                        if (!filterByDate) return true;
                        var inRange = true;
                        if (fromNum && dateNum < fromNum) inRange = false;
                        if (toNumVal && dateNum > toNumVal) inRange = false;
                        return inRange;
                    });
                }
                checkedRows.each(function() {
                    var $row = $(this);
                    var tds = $row.find('td');
                    var commodityName = tds.eq(3).text().trim();
                    var commodityAmount = parseFloat(tds.eq(4).text().replace(/,/g, '')) || 0;
                    var unit = tds.eq(5).text().trim();
                    var orderId = $row.data('order-id');
                    if (commodityName) {
                        // Aggregate order amounts for each product
                        if (!commodityMap[commodityName]) {
                            commodityMap[commodityName] = 0;
                            unitMap[commodityName] = unit;
                        }
                        commodityMap[commodityName] += commodityAmount;
                        // Collect all filtered order_ids for backend use
                        data.orderIds.push(orderId);
                    }
                });
                // Final output: only one entry per product
                data.names = Object.keys(commodityMap);
                data.amounts = data.names.map(function(name) { return commodityMap[name]; });
                data.units = data.names.map(function(name) { return unitMap[name]; });
                return data;
            }

            function renderCharts(data) {
                $('#order-inventory-charts').empty();
                if (data.names.length === 0) {
                    $('#order-inventory-charts').append('<div class="alert alert-info text-center">هیچ سفارشی انتخاب نشده است.</div>');
                    return;
                }
                data.names.forEach(function(name, idx) {
                    var chartId = 'order-inventory-chart-' + idx;
                    $('#order-inventory-charts').append('<div id="'+chartId+'" class="mini-order-chart"></div>');
                    var orderData = {
                        chart: { height: 220, type: "bar" },
                        plotOptions: { bar: { horizontal: false, columnWidth: "55%", endingShape: "rounded" } },
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ["transparent"] },
                        series: [
                            { name: "سفارش", data: [data.amounts[idx]] },
                            { name: "موجودی مواد اولیه", data: [Math.floor(Math.random() * 120)] }
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
            var initialData = getSelectedOrderData();
            renderCharts({
                names: initialData.names,
                amounts: initialData.amounts,
                units: initialData.units
            });

            // Calculate button click handler (Flexible: Table, Mock, Ready for AJAX)
            $('#calculate-orders').on('click', function() {
                var selectedData = getSelectedOrderData();

                if (mockMode) {
                    // Mock mode: generate fake data
                    var names = selectedData.orderIds.map(function(id) { return 'Mock Product ' + id; });
                    var amounts = selectedData.orderIds.map(function() { return Math.floor(Math.random() * 100) + 1; });
                    var units = selectedData.orderIds.map(function() { return 'kg'; });
                    renderCharts({ names, amounts, units });
                } else {
                    // Real mode: use aggregated data from the table
                    renderCharts({
                        names: selectedData.names,
                        amounts: selectedData.amounts,
                        units: selectedData.units
                    });

                    // Later, we can enable AJAX here:
                    /*
                    $.ajax({
                        url: '/orders/chart-data',
                        method: 'POST',
                        data: {
                            order_ids: selectedData.orderIds,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            renderCharts(response);
                        },
                        error: function(xhr) {
                            $('#order-inventory-charts').empty().append('<div class="alert alert-danger">Error fetching data from server</div>');
                        }
                    });
                    */
                }
            });

            // Select all functionality for order checkboxes
            $('#select-all-orders').on('change', function() {
                var checked = $(this).is(':checked');
                $('.order-checkbox').prop('checked', checked);
            });
            
            $(document).on('change', '.order-checkbox', function() {
                var total = $('.order-checkbox').length;
                var checked = $('.order-checkbox:checked').length;
                $('#select-all-orders').prop('checked', total === checked);
            });
        });
    </script>
@endsection