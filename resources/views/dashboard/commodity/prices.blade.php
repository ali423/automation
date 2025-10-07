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
        
        /* Dynamic table styling for better text wrapping */
        #datatable-buttons-commodity-prices {
            table-layout: auto;
        }
        
        #datatable-buttons-commodity-prices td {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            max-width: 200px;
            vertical-align: middle;
        }
        
        #datatable-buttons-commodity-prices th {
            white-space: nowrap;
            vertical-align: middle;
        }
        
        /* Specific styling for title column */
        #datatable-buttons-commodity-prices td:nth-child(4) {
            max-width: 250px;
            text-align: center;
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

            // Function to calculate dynamic column widths based on content
            function calculateDynamicColumnWidths(tableBody, columnCount) {
                if (!tableBody || !tableBody.length) {
                    // Fallback to default widths if no data
                    return Array(columnCount).fill(100 / columnCount + '%');
                }

                var maxLengths = Array(columnCount).fill(0);
                var totalRows = Math.min(tableBody.length, 20); // Analyze up to 20 rows for performance
                
                // Analyze each column to find maximum content length
                for (var row = 0; row < totalRows; row++) {
                    if (tableBody[row] && tableBody[row].length >= columnCount) {
                        for (var col = 0; col < columnCount; col++) {
                            var cellContent = tableBody[row][col].text || '';
                            var contentLength = cellContent.length;
                            
                            // Special handling for different column types (5 columns: unit, sales price, box price, title, row number)
                            if (col === 3) { // Title column - give more space for long titles
                                contentLength = Math.max(contentLength * 0.8, 15); // Minimum 15 chars
                            } else if (col === 0) { // Unit column - usually short
                                contentLength = Math.max(contentLength, 6);
                            } else if (col === 4) { // Row number column - very short
                                contentLength = Math.max(contentLength, 4);
                            } else if (col === 1 || col === 2) { // Sales price and box price columns
                                contentLength = Math.max(contentLength, 10);
                            } else { // Other columns
                                contentLength = Math.max(contentLength, 10);
                            }
                            
                            maxLengths[col] = Math.max(maxLengths[col], contentLength);
                        }
                    }
                }

                // Calculate total length for percentage calculation
                var totalLength = maxLengths.reduce(function(sum, length) {
                    return sum + length;
                }, 0);

                // Convert to percentages with minimum and maximum constraints
                var widths = maxLengths.map(function(length) {
                    var percentage = (length / totalLength) * 100;
                    // Set minimum and maximum constraints
                    percentage = Math.max(percentage, 8);  // Minimum 8%
                    percentage = Math.min(percentage, 35); // Maximum 35%
                    return percentage + '%';
                });

                // Adjust if total exceeds 100% due to constraints
                var totalPercentage = widths.reduce(function(sum, width) {
                    return sum + parseFloat(width);
                }, 0);

                if (totalPercentage > 100) {
                    // Scale down proportionally
                    var scaleFactor = 100 / totalPercentage;
                    widths = widths.map(function(width) {
                        return (parseFloat(width) * scaleFactor) + '%';
                    });
                }

                return widths;
            }

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
                            // Exclude checkbox (0), drag handle (1), base price (6), commodity number (4), commodity id (5), and type (8) when exporting with profit
                            // Use columns: [9,7,3,2] to include unit, sales price, title, and row number
                            columns: [9,7,3,2],
                            format: {
                                body: function (data, row, column, node) {
                                    // Add box price calculation for sales price column (column 7 in original table)
                                    if (column === 7) {
                                        var salesPrice = parseFloat(data.replace(/,/g, ''));
                                        if (!isNaN(salesPrice) && salesPrice > 0) {
                                            // Calculate box price (assuming pieces_per_box = 12)
                                            var boxPrice = salesPrice * 12;
                                            return data + '|' + boxPrice.toLocaleString();
                                        }
                                        return data + '|-';
                                    }
                                    return data;
                                }
                            },
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
                            // Exclude checkbox (0), drag handle (1), base price (6), commodity number (4), commodity id (5), and type (8) when exporting with profit
                            // Use columns: [9,7,3,2] to include unit, sales price, title, and row number
                            columns: [9,7,3,2],
                            format: {
                                body: function (data, row, column, node) {
                                    // Add box price calculation for sales price column (column 7 in original table)
                                    if (column === 7) {
                                        var salesPrice = parseFloat(data.replace(/,/g, ''));
                                        if (!isNaN(salesPrice) && salesPrice > 0) {
                                            // Calculate box price (assuming pieces_per_box = 12)
                                            var boxPrice = salesPrice * 12;
                                            return data + '|' + boxPrice.toLocaleString();
                                        }
                                        return data + '|-';
                                    }
                                    return data;
                                }
                            },
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
                            
                            // Process table data and add box price column
                            if (doc.content && doc.content[1] && doc.content[1].table && doc.content[1].table.body) {
                                var tableBody = doc.content[1].table.body;
                                
                                // Process header row
                                if (tableBody[0]) {
                                    // Change unit column header
                                    if (tableBody[0][0]) {
                                        var headerText = tableBody[0][0].text || '';
                                        if (headerText.includes('واحد اندازه گیری')) {
                                            tableBody[0][0].text = 'واحد کالا';
                                        }
                                    }
                                    
                                    // Change sales price column header and add box price header
                                    if (tableBody[0][1]) {
                                        var salesPriceHeader = tableBody[0][1].text || '';
                                        if (salesPriceHeader.includes('قیمت فروش با احتساب سود')) {
                                            tableBody[0][1].text = 'قیمت نهایی';
                                        }
                                    }
                                    
                                    // Add box price column header
                                    tableBody[0].splice(2, 0, { text: 'قیمت کارتن', style: 'tableHeader' });
                                }
                                
                                // Process data rows
                                for (var i = 1; i < tableBody.length; i++) {
                                    if (tableBody[i] && tableBody[i][1]) {
                                        var salesPriceText = tableBody[i][1].text || '';
                                        
                                        // Extract sales price and box price from formatted data
                                        var parts = salesPriceText.split('|');
                                        var salesPrice = parts[0] || '';
                                        var boxPrice = parts[1] || '-';
                                        
                                        // Update sales price column
                                        tableBody[i][1].text = salesPrice;
                                        
                                        // Add box price column
                                        tableBody[i].splice(2, 0, { 
                                            text: boxPrice, 
                                            style: i % 2 === 0 ? 'tableBodyEven' : 'tableBodyOdd' 
                                        });
                                    }
                                }
                            }
                            
                            // Calculate dynamic column widths based on content (5 columns: unit, sales price, box price, title, row number)
                            var dynamicWidths = calculateDynamicColumnWidths(doc.content[1].table.body, 5);
                            doc.content[1].table.widths = dynamicWidths;
                            
                            // Set table layout to auto for better text wrapping
                            doc.content[1].table.layout = 'auto';
                            
                            // Add custom styles for better text handling
                            doc.styles.tableBodyEven = {
                                alignment: 'center',
                                fontSize: 9,
                                lineHeight: 1.2,
                                margin: [2, 2, 2, 2],
                                fillColor: '#e8f4fd'
                            };
                            doc.styles.tableBodyOdd = {
                                alignment: 'center',
                                fontSize: 9,
                                lineHeight: 1.2,
                                margin: [2, 2, 2, 2],
                                fillColor: '#ffffff'
                            };
                            
                            // Add header styles
                            doc.styles.tableHeader = {
                                alignment: 'center',
                                fontSize: 10,
                                bold: true,
                                fillColor: '#2c3e50',
                                color: 'white',
                                margin: [2, 2, 2, 2]
                            };
                            
                            
                            // Process table body to handle long text with proper wrapping
                            if (doc.content[1] && doc.content[1].table && doc.content[1].table.body) {
                                var tableBody = doc.content[1].table.body;
                                for (var i = 0; i < tableBody.length; i++) {
                                    for (var j = 0; j < tableBody[i].length; j++) {
                                        var cell = tableBody[i][j];
                                        if (cell && cell.text && cell.text.length > 30) {
                                            // For long text, add proper styling for wrapping
                                            cell.style = {
                                                fontSize: 8,
                                                lineHeight: 1.1,
                                                margin: [1, 1, 1, 1]
                                            };
                                        }
                                    }
                                }
                            }
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
                            
                            // Calculate dynamic column widths based on content
                            var dynamicWidths = calculateDynamicColumnWidths(doc.content[1].table.body, 7);
                            doc.content[1].table.widths = dynamicWidths;
                            
                            // Set table layout to auto for better text wrapping
                            doc.content[1].table.layout = 'auto';
                            
                            // Add custom styles for better text handling
                            doc.styles.tableBodyEven = {
                                alignment: 'center',
                                fontSize: 9,
                                lineHeight: 1.2,
                                margin: [2, 2, 2, 2],
                                fillColor: '#e8f4fd'
                            };
                            doc.styles.tableBodyOdd = {
                                alignment: 'center',
                                fontSize: 9,
                                lineHeight: 1.2,
                                margin: [2, 2, 2, 2],
                                fillColor: '#ffffff'
                            };
                            
                            // Add header styles
                            doc.styles.tableHeader = {
                                alignment: 'center',
                                fontSize: 10,
                                bold: true,
                                fillColor: '#2c3e50',
                                color: 'white',
                                margin: [2, 2, 2, 2]
                            };
                            
                            // Process table body to handle long text with proper wrapping
                            if (doc.content[1] && doc.content[1].table && doc.content[1].table.body) {
                                var tableBody = doc.content[1].table.body;
                                for (var i = 0; i < tableBody.length; i++) {
                                    for (var j = 0; j < tableBody[i].length; j++) {
                                        var cell = tableBody[i][j];
                                        if (cell && cell.text && cell.text.length > 30) {
                                            // For long text, add proper styling for wrapping
                                            cell.style = {
                                                fontSize: 8,
                                                lineHeight: 1.1,
                                                margin: [1, 1, 1, 1]
                                            };
                                        }
                                    }
                                }
                            }
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


