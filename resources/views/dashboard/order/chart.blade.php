@extends('layouts.main')
@section('title', 'نمودار سفارشات')
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
                    <h4 class="order-report-section-title mb-0">مواد اولیه مورد نیاز و موجودی</h4>
                    <p class="order-report-section-desc text-muted small mt-2">بر اساس سفارشات انتخاب‌شده در بخش پایین و فیلترهای اعمال‌شده محاسبه می‌شود.</p>
                </div>
                <div class="card-body">
                    <div class="order-report-hint">
                        <i class="ti-info-alt"></i>
                        <span>فقط سفارشات با وضعیت «در حال پردازش» در محاسبات لحاظ می‌شوند. با تغییر فیلترها یا انتخاب سفارش‌ها، این جدول به‌روز می‌شود.</span>
                    </div>
                    <div id="order-inventory-table-wrapper" class="order-report-table-block">
                        <div id="chart-loading" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">در حال بارگذاری...</span>
                            </div>
                            <p class="mt-3 mb-0 text-muted">در حال بارگذاری اطلاعات...</p>
                        </div>
                        <div class="table-responsive">
                            <table id="order-inventory-table" class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>مواد اولیه</th>
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
            <div class="card order-report-section-card">
                <div class="card-header">
                    <h4 class="order-report-section-title mb-0">لیست سفارشات</h4>
                    <p class="order-report-section-desc text-muted small mt-2">فیلترها را اعمال کنید یا سفارش‌های مورد نظر را انتخاب کنید.</p>
                </div>
                <div class="card-body pb-0">
                    <div class="order-report-filters-wrap">
                        <x-pagination-controls :paginator="$orders" :options="$options" />
                    </div>
                    <hr class="order-report-divider">
                    <div id="filter-status" class="text-info small mb-3" style="display: none;"></div>
                    <div class="order-report-table-block">
                    <table id="datatable-buttons-customer" class="table table-striped dt-responsive nowrap w-100 mb-0">
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
                            @php($i = ($orders->currentPage() - 1) * $orders->perPage() + 1)
                            @foreach ($orders as $order)
                                <tr data-order-id="{{ $order->id }}">
                                    <td><input type="checkbox" class="order-checkbox"></td>
                                    <td>{{ $i }}</td>
                                    <td>{{ $order->customer ? $order->customer->name : 'مشتری حذف شده' }}</td>
                                    <td>{{ number_format($order->total_amount) }}</td>
                                    <td>{{ date('Y/m/d', strtotime($order->deadline)) }}</td>
                                    <td>{{ __('fields.order.status.' . $order->status) }}</td>
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
                <div class="card-footer">
                    <x-pagination-navigation :paginator="$orders" />
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

            $('#datatable-buttons-customer').DataTable({
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
                            doc.content[1].table.widths = ['20%','20%', '20%', '20%', '20%', '20%'];
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-primary btn-sm',
                        exportOptions: {
                            columns: [7, 6, 5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-primary btn-sm',
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

            // Keep filter group static to preserve alignment

            // Default state: "همه" is checked, individual checkboxes unchecked
            // When "همه" is checked, ALL orders across ALL pages are included
            $('#select-all-orders').prop('checked', true);
            $('.order-checkbox').prop('checked', false);

            // Remove custom datepicker initialization for date_from and date_to
            // The global $(".usage").persianDatepicker() in bootstrap-datepicker.min.js will handle all .usage fields

            function getSelectedOrderData() {
                var selectAllChecked = $('#select-all-orders').is(':checked');
                
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
                    $('#datatable-buttons-customer tbody tr').each(function() {
                        var $row = $(this);
                        var checkbox = $row.find('.order-checkbox');
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

            function renderTable(data) {
                var $table = $('#order-inventory-table');
                var $tbody = $table.find('tbody');

                if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().destroy();
                }

                $tbody.empty();

                if (!data.names || data.names.length === 0) {
                    $tbody.append('<tr><td colspan="6" class="text-center text-muted">داده‌ای برای نمایش وجود ندارد</td></tr>');
                    return;
                }

                data.names.forEach(function(name, idx) {
                    var need = (data.amounts && data.amounts[idx] !== undefined) ? parseFloat(data.amounts[idx]) : 0;
                    var inv = (data.inventory && data.inventory[idx] !== undefined) ? parseFloat(data.inventory[idx]) : 0;
                    var diff = inv - need;
                    var unit = (data.units && data.units[idx]) ? data.units[idx] : '';
                    var statusOk = parseFloat(inv) >= parseFloat(need);
                    var statusBadge = statusOk
                        ? '<span class="badge badge-success">کافی</span>'
                        : '<span class="badge badge-danger">کمبود</span>';

                    $tbody.append(
                        '<tr>' +
                            '<td>' + name + '</td>' +
                            '<td data-order="' + need + '">' + need + '</td>' +
                            '<td data-order="' + inv + '">' + inv + '</td>' +
                            '<td data-order="' + diff + '">' + diff + '</td>' +
                            '<td>' + unit + '</td>' +
                            '<td>' + statusBadge + '</td>' +
                        '</tr>'
                    );
                });

                if ($.fn.DataTable) {
                    $table.DataTable({
                        dom: 'Bfrtip',
                        paging: false,
                        searching: false,
                        info: false,
                        order: [[1, 'desc']],
                        buttons: inventoryTableButtons
                    });
                }
            }

            // Initial table rendering
            // Behavior: "همه" checkbox selects ALL orders matching current filters (ignores pagination)
            // Filters respected: customer_id, status, search, date_from, date_to
            // Filters ignored: page, per_page (pagination)
            function loadInitialTable() {
                var urlParams = new URLSearchParams(window.location.search);
                
                // Build payload with select_all: true to get ALL data (not just current page)
                var payload = {
                    select_all: true,
                    excluded_ids: [],
                    order_ids: [],
                    date_from: $('#date_from').val() || '',
                    date_to: $('#date_to').val() || '',
                    _token: '{{ csrf_token() }}'
                };
                
                // Include filters from URL (these SHOULD be respected)
                var filtersParam = urlParams.get('filters');
                if (filtersParam) {
                    payload.filters = filtersParam;
                }
                
                var searchParam = urlParams.get('search');
                if (searchParam) {
                    payload.search = searchParam;
                }
                
                // Override date inputs with URL params if present
                var dateFromParam = urlParams.get('date_from');
                var dateToParam = urlParams.get('date_to');
                if (dateFromParam) {
                    payload.date_from = dateFromParam;
                }
                if (dateToParam) {
                    payload.date_to = dateToParam;
                }

                // Show loading indicator
                $('#chart-loading').show();
                $('#order-inventory-table').hide();

                $.ajax({
                    url: '{{ route("order.chart.data") }}',
                    method: 'POST',
                    data: payload,
                    success: function(response) {
                        $('#chart-loading').hide();
                        $('#order-inventory-table').show();
                        renderTable({
                            names: response.names,
                            amounts: response.amounts,
                            units: response.units,
                            inventory: response.inventory
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#chart-loading').hide();
                        $('#order-inventory-table').show();
                        console.error('AJAX Error:', {status: status, error: error, response: xhr.responseText});
                        $('#order-inventory-table tbody').html('<tr><td colspan="6" class="text-center text-danger">خطا در دریافت اطلاعات</td></tr>');
                    }
                });
            }

            // Update table based on checkbox selections
            // Respects: filters (customer_id, status), search, date_from, date_to
            // Ignores: pagination (page, per_page)
            function updateTable() {
                var selectedData = getSelectedOrderData();
                var urlParams = new URLSearchParams(window.location.search);

                var payload = {
                    select_all: selectedData.selectAll,
                    excluded_ids: selectedData.excludedIds,
                    order_ids: selectedData.orderIds,
                    date_from: $('#date_from').val() || '',
                    date_to: $('#date_to').val() || '',
                    _token: '{{ csrf_token() }}'
                };

                // Include filters from URL (these SHOULD be respected)
                var filtersParam = urlParams.get('filters');
                if (filtersParam) {
                    payload.filters = filtersParam;
                }
                
                var searchParam = urlParams.get('search');
                if (searchParam) {
                    payload.search = searchParam;
                }

                // Show loading indicator
                $('#chart-loading').show();
                $('#order-inventory-table').hide();

                $.ajax({
                    url: '{{ route("order.chart.data") }}',
                    method: 'POST',
                    data: payload,
                    success: function(response) {
                        $('#chart-loading').hide();
                        $('#order-inventory-table').show();
                        renderTable({
                            names: response.names,
                            amounts: response.amounts,
                            units: response.units,
                            inventory: response.inventory
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#chart-loading').hide();
                        $('#order-inventory-table').show();
                        console.error('Error fetching table data:', xhr);
                        $('#order-inventory-table tbody').html('<tr><td colspan="6" class="text-center text-danger">خطا در دریافت اطلاعات</td></tr>');
                    }
                });
            }

            // Calculate button click handler: persist dates and reload page (server-side filtering)
            $('#calculate-orders').on('click', function() {
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

            // Select all functionality for order checkboxes
            // When "همه" is checked: ALL orders across ALL pages are included (backend handles this)
            // When "همه" is unchecked: only individually checked orders on current page are included
            var isUpdatingCheckboxes = false; // Flag to prevent cascading events
            
            $('#select-all-orders').on('change', function() {
                if (isUpdatingCheckboxes) return; // Prevent cascading
                isUpdatingCheckboxes = true;
                
                var checked = $(this).is(':checked');
                if (checked) {
                    // When "همه" is checked, uncheck all individual checkboxes
                    // This means "include all orders" with no exclusions
                    $('.order-checkbox').prop('checked', false);
                }
                // When "همه" is unchecked, keep individual checkboxes as they are
                
                isUpdatingCheckboxes = false;
                // Update table automatically when select all changes
                updateTable();
            });
            
            $(document).on('change', '.order-checkbox', function() {
                if (isUpdatingCheckboxes) return; // Prevent cascading
                isUpdatingCheckboxes = true;
                
                var isChecked = $(this).is(':checked');
                
                if (isChecked) {
                    // When user checks an individual checkbox, switch to specific selection mode
                    // Uncheck "همه" to indicate we're now selecting specific orders
                    $('#select-all-orders').prop('checked', false);
                }
                
                isUpdatingCheckboxes = false;
                // Update table automatically when individual checkboxes change
                updateTable();
            });
            
            // Update table when date filters change (with debounce)
            var dateUpdateTimeout;
            $('#date_from, #date_to').on('change', function() {
                clearTimeout(dateUpdateTimeout);
                dateUpdateTimeout = setTimeout(function() {
                    updateTable();
                }, 500);
            });

            // Load initial table data (called after all functions are defined)
            loadInitialTable();
        });
    </script>
@endsection