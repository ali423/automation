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
                    <div class="mt-3" id="factory-table-wrapper">
                        <div class="table-responsive">
                            <table id="factory-inventory-table" class="table table-sm table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>محصول / مواد</th>
                                        <th>نیاز</th>
                                        <th>موجودی</th>
                                        <th>اختلاف</th>
                                        <th>واحد</th>
                                        <th>وضعیت</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
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
                    renderFactoryTable({ names: [], orders: [], inventory: [], units: [] });
                } else {
                    renderFactoryCharts({
                        names: selectedData.names,
                        inventory: selectedData.inventory,
                        orders: selectedData.orders,
                        units: selectedData.units
                    });
                    renderFactoryTable({
                        names: selectedData.names,
                        orders: selectedData.orders,
                        inventory: selectedData.inventory,
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

                // Collect selected order IDs
                var selectedOrderIds = [];
                if (checkedRows.length > 0) {
                    checkedRows.each(function(index) {
                        var $row = $(this);
                        var rowId = parseInt($row.data('order-id'));
                        if (rowId && selectedOrderIds.indexOf(rowId) === -1) {
                            selectedOrderIds.push(rowId);
                        }
                    });
                }

                // Filter aggregated data: include entries where orderIds array contains any selected order ID
                // Calculate ordered amount only for selected orders using orderAmounts mapping
                if (selectedOrderIds.length > 0 && factoryData.length > 0) {
                    factoryData.forEach(function(item) {
                        // Check if this aggregated entry's orderIds array intersects with selected order IDs
                        var hasMatchingOrder = false;
                        var selectedOrderedAmount = 0;
                        
                        if (item.orderIds && Array.isArray(item.orderIds)) {
                            // Check if any selected order contributes to this commodity+unit
                            item.orderIds.forEach(function(orderId) {
                                var orderIdInt = parseInt(orderId);
                                if (selectedOrderIds.indexOf(orderIdInt) !== -1) {
                                    hasMatchingOrder = true;
                                    // Sum the ordered amount for this selected order
                                    // Try both string and integer key (JSON may encode keys differently)
                                    if (item.orderAmounts) {
                                        var amount = item.orderAmounts[orderId] || item.orderAmounts[orderIdInt] || 0;
                                        selectedOrderedAmount += parseFloat(amount) || 0;
                                    }
                                }
                            });
                        } else {
                            // Fallback for old data structure (backward compatibility)
                            if (item.orderId && selectedOrderIds.indexOf(parseInt(item.orderId)) !== -1) {
                                hasMatchingOrder = true;
                                selectedOrderedAmount = parseFloat(item.orderedAmount) || 0;
                            }
                        }
                        
                        if (hasMatchingOrder && selectedOrderedAmount > 0) {
                            // Avoid duplicates by checking if this commodity+unit already exists
                            var existingIndex = data.names.indexOf(item.productName);
                            if (existingIndex === -1) {
                                // New commodity+unit combination
                                data.names.push(item.productName);
                                data.inventory.push(item.inventory);
                                data.orders.push(selectedOrderedAmount);
                                data.units.push(item.unitSymbol || item.unit);
                            } else {
                                // Same commodity+unit from different orders - sum the amounts
                                // Note: This shouldn't happen with proper aggregation, but handle it just in case
                                data.orders[existingIndex] = parseFloat(data.orders[existingIndex]) + selectedOrderedAmount;
                            }
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

        function renderFactoryTable(data) {
            var $table = $('#factory-inventory-table');
            var $tbody = $table.find('tbody');

            if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
            }

            $tbody.empty();

            if (!data.names || data.names.length === 0) {
                $tbody.append('<tr><td colspan="5" class="text-center text-muted">داده‌ای برای نمایش وجود ندارد</td></tr>');
            } else {
                data.names.forEach(function(name, idx) {
                    var orders = (data.orders && data.orders[idx] !== undefined) ? parseFloat(data.orders[idx]) : 0;
                    var inv = (data.inventory && data.inventory[idx] !== undefined) ? parseFloat(data.inventory[idx]) : 0;
                    var unit = (data.units && data.units[idx]) ? data.units[idx] : '';
                    var diff = inv - orders;
                    var statusOk = inv >= orders;
                    var statusBadge = statusOk
                        ? '<span class="badge badge-success">کافی</span>'
                        : '<span class="badge badge-danger">کمبود</span>';

                    $tbody.append(
                        '<tr>' +
                            '<td>' + name + '</td>' +
                            '<td data-order="' + orders + '">' + orders + ' ' + unit + '</td>' +
                            '<td data-order="' + inv + '">' + inv + ' ' + unit + '</td>' +
                            '<td data-order="' + diff + '">' + diff + ' ' + unit + '</td>' +
                            '<td>' + unit + '</td>' +
                            '<td>' + statusBadge + '</td>' +
                        '</tr>'
                    );
                });
            }

            if ($.fn.DataTable) {
                $table.DataTable({
                    paging: false,
                    searching: false,
                    info: false,
                    order: [[1, 'desc']]
                });
            }
        }
    </script>
@endsection
