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
                    <h4 class="card-title mb-2">وضعیت کارخانه</h4>
                    <div id="factory-table-wrapper">
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
                                <th>شماره سفارش</th>
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
                                        <td><input type="checkbox" class="factory-checkbox"></td>
                                        <td>{{ $i }}</td>
                                        <td><strong>#{{ $order->id }}</strong></td>
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
                                            <a href="{{ route('order.factory-status.customer', ['id' => $order->customer->id ?? 0, 'order_id' => $order->id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ti-more-alt"></i> جزئیات
                                            </a>
                                        </td>
                                    </tr>
                                    @php($i++)
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">هیچ سفارش معلقی یافت نشد.</td>
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

            var factoryDataTable = $('#datatable-buttons-factory').DataTable({
                dom: 'Bfrtip',
                paging: false,
                searching: false,
                info: false,
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
                            doc.content[1].table.widths = ['12%','12%', '12%', '12%', '12%', '12%', '14%', '14%'];
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

            // Load full dataset from backend - this contains ALL pending orders aggregated data
            var factoryData = @json($warehouseChartData['orders']);
            
            // Default state: "\u0647\u0645\u0647" is checked, individual checkboxes unchecked
            // When "\u0647\u0645\u0647" is checked, ALL orders across ALL pages are included
            $('#select-all-factory').prop('checked', true);
            var allRows = factoryDataTable.rows().nodes().to$();
            allRows.find('.factory-checkbox').prop('checked', false);
            
            // Initial render - with "\u0647\u0645\u0647" checked, show ALL data
            updateCharts();

            // No client-side date filtering in factory view; server-side filters via shared controls

            // Function to update table based on current filters
            function updateCharts() {
                var selectedData = getSelectedFactoryData();
                
                if (selectedData.names.length === 0) {
                    renderFactoryTable({ names: [], orders: [], inventory: [], units: [] });
                } else {
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

                var selectAllChecked = $('#select-all-factory').is(':checked');

                if (selectAllChecked) {
                    // When "\u0647\u0645\u0647" is checked: include ALL orders from factoryData
                    // Collect excluded order IDs (checked individual checkboxes mean "exclude from all")
                    var excludedOrderIds = [];
                    var allRows = factoryDataTable.rows().nodes().to$();
                    allRows.each(function() {
                        var $row = $(this);
                        var checkbox = $row.find('.factory-checkbox');
                        // If checkbox is CHECKED, it means user wants to EXCLUDE this order
                        if (checkbox.length && checkbox.prop('checked')) {
                            var rowId = parseInt($row.data('order-id'));
                            if (rowId && excludedOrderIds.indexOf(rowId) === -1) {
                                excludedOrderIds.push(rowId);
                            }
                        }
                    });
                    
                    // Process ALL factoryData, excluding specified orders
                    if (factoryData.length > 0) {
                        factoryData.forEach(function(item) {
                            var selectedOrderedAmount = 0;
                            var hasMatchingOrder = false;
                            
                            if (item.orderIds && Array.isArray(item.orderIds)) {
                                // Include amounts from all orders EXCEPT excluded ones
                                item.orderIds.forEach(function(orderId) {
                                    var orderIdInt = parseInt(orderId);
                                    // Include if NOT in excluded list
                                    if (excludedOrderIds.indexOf(orderIdInt) === -1) {
                                        hasMatchingOrder = true;
                                        if (item.orderAmounts) {
                                            var amount = item.orderAmounts[orderId] || item.orderAmounts[orderIdInt] || 0;
                                            selectedOrderedAmount += parseFloat(amount) || 0;
                                        }
                                    }
                                });
                            } else {
                                // Fallback for old data structure
                                if (item.orderId && excludedOrderIds.indexOf(parseInt(item.orderId)) === -1) {
                                    hasMatchingOrder = true;
                                    selectedOrderedAmount = parseFloat(item.orderedAmount) || 0;
                                }
                            }
                            
                            if (hasMatchingOrder && selectedOrderedAmount > 0) {
                                var existingIndex = data.names.indexOf(item.productName);
                                if (existingIndex === -1) {
                                    data.names.push(item.productName);
                                    data.inventory.push(item.inventory);
                                    data.orders.push(selectedOrderedAmount);
                                    data.units.push(item.unitSymbol || item.unit);
                                } else {
                                    data.orders[existingIndex] = parseFloat(data.orders[existingIndex]) + selectedOrderedAmount;
                                }
                            }
                        });
                    }
                } else {
                    // When "\u0647\u0645\u0647" is unchecked: include only checked (selected) orders
                    var selectedOrderIds = [];
                    var allRows = factoryDataTable.rows().nodes().to$();
                    var checkedRows = allRows.filter(function() {
                        var checkbox = $(this).find('.factory-checkbox');
                        return checkbox.length && checkbox.prop('checked');
                    });

                    checkedRows.each(function() {
                        var $row = $(this);
                        var rowId = parseInt($row.data('order-id'));
                        if (rowId && selectedOrderIds.indexOf(rowId) === -1) {
                            selectedOrderIds.push(rowId);
                        }
                    });
                    
                    // Process factoryData, including only selected orders
                    if (selectedOrderIds.length > 0 && factoryData.length > 0) {
                        factoryData.forEach(function(item) {
                            var selectedOrderedAmount = 0;
                            var hasMatchingOrder = false;
                            
                            if (item.orderIds && Array.isArray(item.orderIds)) {
                                item.orderIds.forEach(function(orderId) {
                                    var orderIdInt = parseInt(orderId);
                                    if (selectedOrderIds.indexOf(orderIdInt) !== -1) {
                                        hasMatchingOrder = true;
                                        if (item.orderAmounts) {
                                            var amount = item.orderAmounts[orderId] || item.orderAmounts[orderIdInt] || 0;
                                            selectedOrderedAmount += parseFloat(amount) || 0;
                                        }
                                    }
                                });
                            } else {
                                if (item.orderId && selectedOrderIds.indexOf(parseInt(item.orderId)) !== -1) {
                                    hasMatchingOrder = true;
                                    selectedOrderedAmount = parseFloat(item.orderedAmount) || 0;
                                }
                            }
                            
                            if (hasMatchingOrder && selectedOrderedAmount > 0) {
                                var existingIndex = data.names.indexOf(item.productName);
                                if (existingIndex === -1) {
                                    data.names.push(item.productName);
                                    data.inventory.push(item.inventory);
                                    data.orders.push(selectedOrderedAmount);
                                    data.units.push(item.unitSymbol || item.unit);
                                } else {
                                    data.orders[existingIndex] = parseFloat(data.orders[existingIndex]) + selectedOrderedAmount;
                                }
                            }
                        });
                    }
                }

                return data;
            }

            // Calculate button click handler
            $('#calculate-factory').on('click', function() {
                filterTableByDate();
                updateCharts();
            });

            // Select all functionality for factory checkboxes
            // When "\u0647\u0645\u0647" is checked: ALL orders are included, individual checkboxes mark exclusions
            // When "\u0647\u0645\u0647" is unchecked: only checked individual orders are included
            $('#select-all-factory').on('change', function() {
                var checked = $(this).is(':checked');
                var allRows = factoryDataTable.rows().nodes().to$();
                if (checked) {
                    // When "\u0647\u0645\u0647" is checked, uncheck all individual checkboxes (no exclusions)
                    allRows.find('.factory-checkbox').prop('checked', false);
                } else {
                    // When "\u0647\u0645\u0647" is unchecked, uncheck all (user must manually select)
                    allRows.find('.factory-checkbox').prop('checked', false);
                }
                updateCharts();
            });
            
            $(document).on('change', '.factory-checkbox', function() {
                var selectAllChecked = $('#select-all-factory').is(':checked');
                
                if (selectAllChecked) {
                    // When "\u0647\u0645\u0647" is checked, individual checkbox changes mean exclude/include
                    // Keep "\u0647\u0645\u0647" checked - individual checkboxes mark exclusions
                } else {
                    // When "\u0647\u0645\u0647" is not checked, individual checkboxes mark inclusions
                    // (Optional: auto-check "\u0647\u0645\u0647" if all visible are checked - disabled for now)
                }
                updateCharts();
            });
        });

        var inventoryDataTable = null;
        
        function renderFactoryTable(data) {
            console.log('Rendering factory table with data:', data);
            
            var $table = $('#factory-inventory-table');
            var $tbody = $table.find('tbody');

            // Properly destroy existing DataTable if it exists
            if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                try {
                    if (inventoryDataTable !== null) {
                        inventoryDataTable.destroy();
                    } else {
                        $table.DataTable().destroy();
                    }
                    inventoryDataTable = null;
                } catch(e) {
                    console.log('Error destroying table:', e);
                    inventoryDataTable = null;
                }
            }

            // Clear table body
            $tbody.empty();

            if (!data.names || data.names.length === 0) {
                $tbody.append('<tr><td colspan="6" class="text-center text-muted">هیچ داده‌ای برای نمایش وجود ندارد</td></tr>');
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

            // Reinitialize DataTable with destroy option to allow reinitialization
            if ($.fn.DataTable) {
                try {
                    inventoryDataTable = $table.DataTable({
                        destroy: true,  // Allow reinitialization
                        paging: false,
                        searching: false,
                        info: false,
                        order: [[1, 'desc']]
                    });
                } catch(e) {
                    console.log('Error initializing table:', e);
                }
            }
        }
    </script>
@endsection
