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
    <link rel="stylesheet" href="{{ asset('css/order-report-pages.css') }}">
@endsection

@section('content')
    <div class="order-report-page">
    <div class="row g-4">
        <div class="col-12 box-margin">
            <div class="card order-report-section-card">
                <div class="card-header">
                    <h4 class="order-report-section-title mb-0">وضعیت کارخانه</h4>
                    <p class="order-report-section-desc text-muted small mt-2">خلاصه نیاز و موجودی محصولات بر اساس سفارشات فیلترشده.</p>
                </div>
                <div class="card-body">
                    <div class="order-report-hint">
                        <i class="ti-info-alt"></i>
                        <span>این جدول با تغییر فیلترها یا انتخاب سفارش‌ها در بخش پایین به‌روز می‌شود.</span>
                    </div>
                    <div id="factory-table-wrapper" class="order-report-table-block">
                        <div id="factory-chart-loading" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">در حال بارگذاری...</span>
                            </div>
                            <p class="mt-3 mb-0 text-muted">در حال بارگذاری اطلاعات...</p>
                        </div>
                        <div class="table-responsive">
                            <table id="factory-inventory-table" class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>محصول / مواد</th>
                                        <th>نیاز</th>
                                        <th>موجودی</th>
                                        <th>اختلاف</th>
                                        <th>اختلاف بر اساس بسته بندی</th>
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
            <div class="card order-report-section-card">
                <div class="card-header">
                    <h4 class="order-report-section-title mb-0">ارزیابی تحویل سفارشات</h4>
                    <p class="order-report-section-desc text-muted small mt-2">بر اساس موجودی انبار و سفارشات در حال پردازش.</p>
                </div>
                <div class="card-body pb-0">
                    <div class="order-report-summary">
                        @include('dashboard.order.partials.order-summary-stats', ['summaryStats' => $summaryStats])
                    </div>
                    <div class="order-report-filters-wrap">
                        <x-pagination-controls :paginator="$pendingOrders" :options="$options" />
                    </div>
                    <hr class="order-report-divider">
                    <div id="filter-status" class="text-info small mb-3" style="display: none;"></div>
                    <div class="order-report-table-block">
                    <table id="datatable-buttons-factory" class="table table-striped dt-responsive nowrap w-100 mb-0">
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
                </div>
                <div class="card-footer">
                    <x-pagination-navigation :paginator="$pendingOrders" />
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
    <script src="{{ asset('js/default-assets/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.print.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.sorting.persian.js') }}"></script>

    <script>
        var inventoryTableButtons = [
            { extend: 'copy', text: 'کپی', className: 'btn btn-outline-primary btn-sm' },
            {
                extend: 'pdf',
                text: 'pdf',
                className: 'btn btn-outline-primary btn-sm',
                customize: function(doc) {
                    doc.defaultStyle.font = 'IRANSansWeb';
                    doc.styles.tableBodyEven.alignment = 'center';
                    doc.styles.tableBodyOdd.alignment = 'center';
                }
            },
            { extend: 'excel', className: 'btn btn-outline-primary btn-sm' },
            { extend: 'csv', className: 'btn btn-outline-primary btn-sm' },
            { extend: 'print', text: 'پرینت', className: 'btn btn-outline-primary btn-sm' }
        ];

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
                        className: 'btn btn-outline-primary btn-sm',
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
                        className: 'btn btn-outline-primary btn-sm',
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
                        className: 'btn btn-outline-primary btn-sm',
                        exportOptions: {
                            columns: [6, 5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-primary btn-sm',
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
                        className: 'btn btn-outline-primary btn-sm',
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

            // Default state: "همه" is checked, individual checkboxes unchecked
            // When "همه" is checked, ALL orders across ALL pages are included
            $('#select-all-factory').prop('checked', true);
            $('.factory-checkbox').prop('checked', false);

            // Initial table rendering
            // Behavior: "همه" checkbox selects ALL orders matching current filters (ignores pagination)
            // Filters respected: customer_id, status, attributes, search, date_from, date_to
            // Filters ignored: page, per_page (pagination)
            function loadInitialTable() {
                var urlParams = new URLSearchParams(window.location.search);
                
                // Build payload with select_all: true to get ALL data (not just current page)
                var payload = {
                    select_all: true,
                    excluded_ids: [],
                    order_ids: [],
                    _token: '{{ csrf_token() }}'
                };
                
                var filtersPayload = window.getFiltersPayloadForAjax && window.getFiltersPayloadForAjax();
                if (filtersPayload) {
                    payload.filters = filtersPayload;
                }

                var searchParam = urlParams.get('search');
                if (searchParam) {
                    payload.search = searchParam;
                }

                var dateFromParam = urlParams.get('date_from');
                var dateToParam = urlParams.get('date_to');
                if (dateFromParam) {
                    payload.date_from = dateFromParam;
                }
                if (dateToParam) {
                    payload.date_to = dateToParam;
                }

                // Show loading indicator
                $('#factory-chart-loading').show();
                $('#factory-inventory-table').hide();

                $.ajax({
                    url: '{{ route("order.factory-status.data") }}',
                    method: 'POST',
                    data: payload,
                    success: function(response) {
                        $('#factory-chart-loading').hide();
                        $('#factory-inventory-table').show();
                        renderFactoryTable(response.orders || []);
                    },
                    error: function(xhr, status, error) {
                        $('#factory-chart-loading').hide();
                        $('#factory-inventory-table').show();
                        console.error('Error fetching factory data:', xhr);
                        $('#factory-inventory-table tbody').html('<tr><td colspan="6" class="text-center text-danger">خطا در دریافت اطلاعات</td></tr>');
                    }
                });
            }

            // Update table based on checkbox selections
            // Respects: filters (customer_id, status, attributes), search, date_from, date_to
            // Ignores: pagination (page, per_page)
            function updateTable() {
                var selectedData = getSelectedFactoryData();
                var urlParams = new URLSearchParams(window.location.search);

                var payload = {
                    select_all: selectedData.selectAll,
                    excluded_ids: selectedData.excludedIds,
                    order_ids: selectedData.orderIds,
                    _token: '{{ csrf_token() }}'
                };

                var filtersPayload = window.getFiltersPayloadForAjax && window.getFiltersPayloadForAjax();
                if (filtersPayload) {
                    payload.filters = filtersPayload;
                }

                var searchParam = urlParams.get('search');
                if (searchParam) {
                    payload.search = searchParam;
                }

                var dateFromParam = urlParams.get('date_from');
                var dateToParam = urlParams.get('date_to');
                if (dateFromParam) {
                    payload.date_from = dateFromParam;
                }
                if (dateToParam) {
                    payload.date_to = dateToParam;
                }

                // Show loading indicator
                $('#factory-chart-loading').show();
                $('#factory-inventory-table').hide();

                $.ajax({
                    url: '{{ route("order.factory-status.data") }}',
                    method: 'POST',
                    data: payload,
                    success: function(response) {
                        $('#factory-chart-loading').hide();
                        $('#factory-inventory-table').show();
                        renderFactoryTable(response.orders || []);
                    },
                    error: function(xhr, status, error) {
                        $('#factory-chart-loading').hide();
                        $('#factory-inventory-table').show();
                        console.error('Error fetching factory data:', xhr);
                        $('#factory-inventory-table tbody').html('<tr><td colspan="6" class="text-center text-danger">خطا در دریافت اطلاعات</td></tr>');
                    }
                });
            }

            function getSelectedFactoryData() {
                var selectAllChecked = $('#select-all-factory').is(':checked');
                
                // When "همه" is checked: include ALL orders across ALL pages (no exclusions)
                // When "همه" is unchecked: include only specifically checked orders on current page
                
                if (selectAllChecked) {
                    // "همه" is checked = ALL orders, no exclusions
                    return {
                        selectAll: true,
                        excludedIds: [],
                        orderIds: []
                    };
                } else {
                    // Collect only checked order IDs
                    var orderIds = [];
                    $('#datatable-buttons-factory tbody tr').each(function() {
                        var $row = $(this);
                        var checkbox = $row.find('.factory-checkbox');
                        if (checkbox.length && checkbox.is(':checked')) {
                            var orderId = $row.data('order-id');
                            if (orderId) {
                                orderIds.push(orderId);
                            }
                        }
                    });
                    
                    return {
                        selectAll: false,
                        excludedIds: [],
                        orderIds: orderIds
                    };
                }
            }

            // Calculate button click handler: persist filters and reload page (server-side filtering)
            $('#calculate-factory').on('click', function() {
                const url = new URL(window.location);
                const df = $('#date_from').val();
                const dt = $('#date_to').val();
                if (df) { url.searchParams.set('date_from', df); } else { url.searchParams.delete('date_from'); }
                if (dt) { url.searchParams.set('date_to', dt); } else { url.searchParams.delete('date_to'); }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            });
            
            // Clear filters button click handler: remove ALL filters and reload
            $('#clear-filters').on('click', function() {
                const url = new URL(window.location);
                // Remove all filter-related query params
                url.searchParams.delete('date_from');
                url.searchParams.delete('date_to');
                url.searchParams.delete('search');
                url.searchParams.delete('filters');
                url.searchParams.delete('page');
                url.searchParams.delete('per_page');
                window.location.href = url.toString();
            });

            // Select all functionality for factory checkboxes
            // When "همه" is checked: ALL orders across ALL pages are included (backend handles this)
            // When "همه" is unchecked: only individually checked orders on current page are included
            var isUpdatingCheckboxes = false; // Flag to prevent cascading events
            
            $('#select-all-factory').on('change', function() {
                if (isUpdatingCheckboxes) return; // Prevent cascading
                isUpdatingCheckboxes = true;
                
                var checked = $(this).is(':checked');
                if (checked) {
                    // When "همه" is checked, uncheck all individual checkboxes
                    // This means "include all orders" with no exclusions
                    $('.factory-checkbox').prop('checked', false);
                }
                // When "همه" is unchecked, keep individual checkboxes as they are
                
                isUpdatingCheckboxes = false;
                // Update table automatically when select all changes
                updateTable();
            });
            
            $(document).on('change', '.factory-checkbox', function() {
                if (isUpdatingCheckboxes) return; // Prevent cascading
                isUpdatingCheckboxes = true;
                
                var isChecked = $(this).is(':checked');
                
                if (isChecked) {
                    // When user checks an individual checkbox, switch to specific selection mode
                    // Uncheck "همه" to indicate we're now selecting specific orders
                    $('#select-all-factory').prop('checked', false);
                }
                
                isUpdatingCheckboxes = false;
                // Update table automatically when individual checkboxes change
                updateTable();
            });

            // Load initial table data (called after all functions are defined)
            loadInitialTable();
        });

        var inventoryDataTable = null;
        
        function renderFactoryTable(data) {
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

            if (!data || data.length === 0) {
                $tbody.append('<tr><td colspan="7" class="text-center text-muted">هیچ داده‌ای برای نمایش وجود ندارد</td></tr>');
            } else {
                // Process the orders array from the backend
                data.forEach(function(item) {
                    var orders = parseFloat(item.orderedAmount) || 0;
                    var inv = parseFloat(item.inventory) || 0;
                    var unit = item.unitSymbol || item.unit || '';
                    var name = item.productName || 'نامشخص';
                    var diff = inv - orders;
                    var piecesPerBox = parseFloat(item.piecesPerBox) || 0;
                    var packagingDiff = piecesPerBox > 0 ? Math.floor(diff / piecesPerBox) : 0;
                    var statusOk = inv >= orders;
                    var statusBadge = statusOk
                        ? '<span class="badge badge-success">کافی</span>'
                        : '<span class="badge badge-danger">کمبود</span>';

                    $tbody.append(
                        '<tr>' +
                            '<td>' + name + '</td>' +
                            '<td data-order="' + orders + '">' + orders + '</td>' +
                            '<td data-order="' + inv + '">' + inv + '</td>' +
                            '<td data-order="' + diff + '">' + diff + '</td>' +
                            '<td data-order="' + packagingDiff + '">' + packagingDiff + '</td>' +
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
                        dom: 'Bfrtip',
                        paging: false,
                        searching: false,
                        info: false,
                        order: [[1, 'desc']],
                        buttons: inventoryTableButtons,
                        columnDefs: [
                            { targets: 4, className: 'dt-body-center' }
                        ]
                    });
                } catch(e) {
                    console.log('Error initializing table:', e);
                }
            }
        }
    </script>
@endsection
