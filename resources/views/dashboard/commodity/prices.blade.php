@extends('layouts.main')
@section('title', 'قیمت‌ها')
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/default-assets/datatables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/select.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables-td.css') }}">
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
                                        <tr>
                                            <td><input type="checkbox" class="row-select" value="{{ $c->id }}" data-id="{{ $c->id }}"></td>
                                            <td>{{ $i }}</td>
                                            <td>{{ $c->title }}</td>
                                            <td>{{ $c->number }}</td>
                                            <td>{{ $c->type == 'product' ? ($c->product_identifier ?? '-') : '-' }}</td>
                                            <td>{{ number_format($c->base_price ?? 0) }}</td>
                                            <td>{{ $c->sales_price !== null ? number_format($c->sales_price) : '-' }}</td>
                                            <td>{{ __('fields.commodity.types')[$c->type] }}</td>
                                            <td>{{ $c->unit ? $c->unit->name . ' (' . $c->unit->symbol . ')' : '-' }}</td>
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
    <script src="{{ asset('js/default-assets/dataTables.sorting.persian.js') }}"></script>
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
                        extend: 'csv',
                        text: 'دانلود (با سود) - CSV',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            // Exclude checkbox (0) and base price (5) when exporting with profit
                            columns: [8,7,6,4,3,2,1],
                            rows: function (idx, data, node) {
                                return $(node).find('.row-select').prop('checked');
                            },
                            modifier: { page: 'all' },
                            orthogonal: 'rtlexport'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'دانلود (با سود) - PDF',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            // Exclude checkbox (0) and base price (5) when exporting with profit
                            columns: [8,7,6,4,3,2,1],
                            rows: function (idx, data, node) {
                                return $(node).find('.row-select').prop('checked');
                            },
                            modifier: { page: 'all' },
                            orthogonal: 'rtlexport'
                        },
                        customize: function (doc) {
                            doc.defaultStyle.font = 'IRANSansWeb';
                            // 7 columns widths after removing base price
                            doc.content[1].table.widths = ['12%', '20%', '18%', '18%', '14%', '12%', '6%'];
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';
                        }
                    },
                    {
                        extend: 'csv',
                        text: 'دانلود (بدون سود) - CSV',
                        className: 'btn btn-outline-secondary',
                        exportOptions: {
                            // Exclude checkbox (0) and sales price (6) when exporting without profit
                            columns: [8,7,5,4,3,2,1],
                            rows: function (idx, data, node) {
                                return $(node).find('.row-select').prop('checked');
                            },
                            modifier: { page: 'all' },
                            orthogonal: 'rtlexport'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'دانلود (بدون سود) - PDF',
                        className: 'btn btn-outline-secondary',
                        exportOptions: {
                            // Exclude checkbox (0) and sales price (6) when exporting without profit
                            columns: [8,7,5,4,3,2,1],
                            rows: function (idx, data, node) {
                                return $(node).find('.row-select').prop('checked');
                            },
                            modifier: { page: 'all' },
                            orthogonal: 'rtlexport'
                        },
                        customize: function (doc) {
                            doc.defaultStyle.font = 'IRANSansWeb';
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
    </script>
    
@endsection


