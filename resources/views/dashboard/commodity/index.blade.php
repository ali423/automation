@extends('layouts.main')
@section('title', 'لیست کالا ها')
@section('page_styles')
    <!-- These plugins only need for the run this page -->
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">لیست کالا ها</h4>
                        <div class="d-flex align-items-center gap-3">
                            <!-- Per Page Selection -->
                            <div class="d-flex align-items-center">
                                <label for="per-page" class="form-label mb-0 me-2">{{ __('pagination.per_page') }}:</label>
                                <select id="per-page" class="form-select form-select-sm" style="width: auto;">
                                    @foreach([10, 25, 50, 100] as $perPage)
                                        <option value="{{ $perPage }}" {{ $commodities->perPage() == $perPage ? 'selected' : '' }}>
                                            {{ $perPage }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search and Filters -->
                    <x-pagination-controls :paginator="$commodities" :options="$options" />
                    
                    @include('dashboard.commodity.partials.commodity-table')

                </div> <!-- end card body-->
                
                <!-- Pagination Navigation -->
                <div class="card-footer">
                    <x-pagination-navigation :paginator="$commodities" />
                </div>
            </div> <!-- end card -->
        </div><!-- end col-->
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
            // Per page change handler for main selector
            $('#per-page').on('change', function() {
                const url = new URL(window.location);
                url.searchParams.set('per_page', this.value);
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });
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

            $('#datatable-buttons-commodity').DataTable({
                dom: 'Bfrtip',
                paging: false, // Disable DataTables pagination since we're using server-side pagination
                searching: false, // Disable DataTables search since we're using server-side search
                ordering: false, // Disable DataTables sorting since we're using server-side sorting
                buttons: [{
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
                        customize: function (doc) {
                            doc.defaultStyle.font = "IRANSansWeb";
                            doc.content[1].table.widths = ['10%', '20%', '15%', '15%', '15%', '10%', '10%', '5%'];
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
                    render: function (data, type, row) {
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
        });
    </script>

@endsection
