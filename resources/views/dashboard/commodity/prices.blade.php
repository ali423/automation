@extends('layouts.main')
@section('title', 'قیمت‌ها')
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/default-assets/datatables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/select.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables-td.css') }}">
    <style>
        .sortable-handle {
            cursor: move;
            color: #6c757d;
            font-size: 16px;
            padding: 5px;
            user-select: none;
        }
        .sortable-handle:hover {
            color: #495057;
        }
        .sortable-row {
            transition: background-color 0.2s ease;
        }
        .sortable-row:hover {
            background-color: #f8f9fa;
        }
        .sortable-row.dragging {
            opacity: 0.5;
            background-color: #e3f2fd;
        }
        .sortable-ghost {
            opacity: 0.4;
            background-color: #e3f2fd;
        }
        .sortable-chosen {
            background-color: #e3f2fd;
        }
        .sortable-drag {
            background-color: #e3f2fd;
        }
        .sortable-placeholder {
            background-color: #e3f2fd;
            border: 2px dashed #2196f3;
            height: 50px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">قیمت‌ها</h4>

                    {{-- Tabs header --}}
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('commodity.index', request()->query()) }}">لیست کالاها</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">قیمت‌ها</a>
                        </li>
                    </ul>

                    {{-- Filters --}}
                    {{-- Pagination Controls (with filters/search) --}}
                    @isset($commodities)
                        <x-pagination-controls :paginator="$commodities" :options="$options" />
                    @endisset

                    <div class="table-responsive">
                        <table id="datatable-buttons-commodity-prices" class="table table-striped dt-responsive nowrap w-100">
                            <thead class="text-center">
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>ترتیب</th>
                                    <th>ردیف</th>
                                    <th>{{ __('fields.title') }}</th>
                                    <th>{{ __('fields.commodity.number') }}</th>
                                    <th>شناسه کالا</th>
                                    <th>{{ __('fields.base_price') }}</th>
                                    <th>قیمت فروش با احتساب سود</th>
                                    <th>{{ __('fields.type') }}</th>
                                    <th>{{ __('fields.unit') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @isset($commodities)
                                    @php($i = ($commodities->currentPage() - 1) * $commodities->perPage() + 1)
                                    @foreach($commodities as $c)
                                        <tr class="sortable-row" data-id="{{ $c->id }}">
                                            <td><input type="checkbox" class="row-select" value="{{ $c->id }}" data-id="{{ $c->id }}"></td>
                                            <td><span class="sortable-handle">⋮⋮</span></td>
                                            <td>{{ $i }}</td>
                                            <td>{{ $c->title }}</td>
                                            <td>{{ $c->number }}</td>
                                            <td>{{ $c->type == 'product' ? ($c->product_identifier ?? '-') : '-' }}</td>
                                            <td>{{ number_format($c->base_price ?? 0) }}</td>
                                            <td>{{ $c->sales_price !== null ? number_format($c->sales_price) : '-' }}</td>
                                            <td>{{ __('fields.commodity.types')[$c->type] }}</td>
                                            <td>{{ $c->unit ? $c->unit->name : '-' }}</td>
                                        </tr>
                                        @php($i++)
                                    @endforeach
                                @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
                @isset($commodities)
                    <div class="card-footer">
                        <x-pagination-navigation :paginator="$commodities" />
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/jszip.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/default-assets/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.print.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="{{ asset('js/default-assets/dataTables.sorting.persian.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
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

            // Initialize sortable functionality
            initializeSortable();

            const table = $('#datatable-buttons-commodity-prices').DataTable({
                dom: 'Bfrtip',
                paging: false, // server pagination used
                searching: false,
                ordering: true,
                order: [],
                info: false,
                data: null,
                buttons: [
                    {
                        extend: 'excel',
                        text: 'دانلود (با سود) - Excel',
                        className: 'btn btn-outline-success',
                        exportOptions: {
                            // Exclude checkbox (0), drag handle (1), and base price (6) when exporting with profit
                            // Use columns: [9,8,7,5,4,3,2] to include sales price with profit
                            columns: [9,8,7,5,4,3,2],
                            rows: function (idx, data, node) {
                                return $(node).find('.row-select').prop('checked');
                            },
                            modifier: { page: 'all' }
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'دانلود (با سود) - PDF',
                        className: 'btn btn-outline-primary',
                        title: 'لیست قیمت محصولات',
                        exportOptions: {
                            // Exclude checkbox (0), drag handle (1), and base price (6) when exporting with profit
                            // Use columns: [9,8,7,5,4,3,2] to include sales price with profit
                            columns: [9,8,7,5,4,3,2],
                            rows: function (idx, data, node) {
                                return $(node).find('.row-select').prop('checked');
                            },
                            modifier: { page: 'all' },
                            orthogonal: 'rtlexport'
                        },
                        customize: function (doc) {
                            doc.defaultStyle.font = 'IRANSansWeb';
                            doc.info = doc.info || {};
                            var titleText = 'لیست قیمت محصولات';
                            doc.info.title = titleText;
                            if (doc.content && doc.content.length > 0 && doc.content[0].text !== undefined) {
                                // Centered header with RTL visual fix (reverse words)
                                var rtlTitle = titleText.split(' ').reverse().join(' ');
                                doc.content[0] = { text: rtlTitle, alignment: 'center', margin: [0, 0, 0, 12] };
                            }
                            
                            // Change header text for unit column (first column in export)
                            if (doc.content && doc.content[1] && doc.content[1].table && doc.content[1].table.body) {
                                var tableBody = doc.content[1].table.body;
                                if (tableBody[0] && tableBody[0][0]) {
                                    // Check if it contains the unit text and replace it
                                    var headerText = tableBody[0][0].text || '';
                                    if (headerText.includes('واحد اندازه گیری')) {
                                        tableBody[0][0].text = 'واحد کالا';
                                    }
                                }
                            }
                            
                            // 7 columns widths after removing checkbox, drag handle, and base price
                            doc.content[1].table.widths = ['12%', '20%', '18%', '18%', '14%', '12%', '6%'];
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'دانلود (بدون سود) - Excel',
                        className: 'btn btn-outline-success',
                        exportOptions: {
                            // Exclude checkbox (0), drag handle (1), and sales price (7) when exporting without profit
                            // Use columns: [9,8,6,5,4,3,2] to exclude sales price
                            columns: [9,8,6,5,4,3,2],
                            rows: function (idx, data, node) {
                                return $(node).find('.row-select').prop('checked');
                            },
                            modifier: { page: 'all' }
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'دانلود (بدون سود) - PDF',
                        className: 'btn btn-outline-secondary',
                        title: 'جدول قیمت های تمام شده محصولات',
                        exportOptions: {
                            // Exclude checkbox (0), drag handle (1), and sales price (7) when exporting without profit
                            // Use columns: [9,8,6,5,4,3,2] to exclude sales price
                            columns: [9,8,6,5,4,3,2],
                            rows: function (idx, data, node) {
                                return $(node).find('.row-select').prop('checked');
                            },
                            modifier: { page: 'all' },
                            orthogonal: 'rtlexport',
                            format: {
                                body: function (data, row, column, node) {
                                    // Column 9 is the unit column in the source table
                                    if (column === 9 && typeof data === 'string') {
                                        // Remove symbol e.g., "نام واحد (SYM)" -> "نام واحد"
                                        return data.split('(')[0].trim();
                                    }
                                    return data;
                                }
                            }
                        },
                        customize: function (doc) {
                            doc.defaultStyle.font = 'IRANSansWeb';
                            doc.info = doc.info || {};
                            var titleText = 'جدول قیمت های تمام شده محصولات';
                            doc.info.title = titleText;
                            if (doc.content && doc.content.length > 0 && doc.content[0].text !== undefined) {
                                // Centered header with RTL visual fix (reverse words)
                                var rtlTitle = titleText.split(' ').reverse().join(' ');
                                doc.content[0] = { text: rtlTitle, alignment: 'center', margin: [0, 0, 0, 12] };
                            }
                            
                            // Change header text for unit column (first column in export)
                            if (doc.content && doc.content[1] && doc.content[1].table && doc.content[1].table.body) {
                                var tableBody = doc.content[1].table.body;
                                if (tableBody[0] && tableBody[0][0]) {
                                    // Check if it contains the unit text and replace it
                                    var headerText = tableBody[0][0].text || '';
                                    if (headerText.includes('واحد اندازه گیری')) {
                                        tableBody[0][0].text = 'واحد کالا';
                                    }
                                }
                            }
                            
                            doc.content[1].table.widths = ['10%', '20%', '15%', '15%', '15%', '10%', '15%'];
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';
                        }
                    }
                ],
                columnDefs: [{
                    targets: '_all',
                    render: function (data, type, row) {
                        if (type === 'rtlexport' && typeof data === 'string') {
                            return data.split(' ').reverse().join(' ');
                        }
                        return data;
                    }
                }],
                language: {
                    paginate: { previous: 'قبلی', next: 'بعدی' }
                }
            });

            // No client-side reload; server pagination/filters handled by form submit

            // Buttons are configured in DataTables init above

            // Select/Deselect all checkboxes
            $('#select-all').on('change', function () {
                const checked = $(this).is(':checked');
                $('.row-select').prop('checked', checked);
            });
        });

        // Sortable functionality
        function initializeSortable() {
            const tbody = document.querySelector('#datatable-buttons-commodity-prices tbody');
            if (!tbody) return;

            // Load saved order from localStorage
            loadSavedOrder();

            const sortable = Sortable.create(tbody, {
                handle: '.sortable-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onStart: function (evt) {
                    evt.item.classList.add('dragging');
                },
                onEnd: function (evt) {
                    evt.item.classList.remove('dragging');
                    updateRowNumbers();
                    saveOrder();
                }
            });
        }

        function loadSavedOrder() {
            const savedOrder = localStorage.getItem('commodity-prices-order');
            if (!savedOrder) return;

            try {
                const order = JSON.parse(savedOrder);
                const tbody = document.querySelector('#datatable-buttons-commodity-prices tbody');
                if (!tbody) return;

                // Create a map of current rows by data-id
                const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                const rowMap = {};
                rows.forEach(row => {
                    rowMap[row.getAttribute('data-id')] = row;
                });

                // Reorder rows according to saved order
                order.forEach(id => {
                    if (rowMap[id]) {
                        tbody.appendChild(rowMap[id]);
                    }
                });

                updateRowNumbers();
            } catch (e) {
                console.error('Error loading saved order:', e);
            }
        }

        function saveOrder() {
            const rows = document.querySelectorAll('#datatable-buttons-commodity-prices tbody tr[data-id]');
            const order = Array.from(rows).map(row => row.getAttribute('data-id'));
            localStorage.setItem('commodity-prices-order', JSON.stringify(order));
        }

        function updateRowNumbers() {
            const rows = document.querySelectorAll('#datatable-buttons-commodity-prices tbody tr[data-id]');
            rows.forEach((row, index) => {
                const rowNumberCell = row.querySelector('td:nth-child(3)'); // Third column is row number
                if (rowNumberCell) {
                    rowNumberCell.textContent = index + 1;
                }
            });
        }

        // Function to get current order for PDF export
        function getCurrentOrder() {
            const rows = document.querySelectorAll('#datatable-buttons-commodity-prices tbody tr[data-id]');
            return Array.from(rows).map(row => row.getAttribute('data-id'));
        }
    </script>
    
@endsection


