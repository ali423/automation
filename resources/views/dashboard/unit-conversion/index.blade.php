@extends('layouts.main')
@section('title', __('fields.conversion_list'))
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
                    <h4 class="card-title mb-2">{{ __('fields.conversion_list') }}</h4>
                    
                    {{-- Pagination Controls --}}
                    <x-pagination-controls :paginator="$conversions" :options="$options" />
                    
                    @if($conversions->count() > 0)
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="ti-info-circle"></i> 
                                مرتب‌سازی فقط برای رکوردهای صفحه فعلی اعمال می‌شود
                            </small>
                        </div>
                        @include('dashboard.unit-conversion.partials.conversion-table')
                        
                        {{-- Pagination Links --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                نمایش {{ $conversions->firstItem() }} تا {{ $conversions->lastItem() }} از {{ $conversions->total() }} رکورد
                            </div>
                            <div>
                                {{ $conversions->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti-info-circle font-48 text-muted"></i>
                            <h5 class="mt-3 text-muted">{{ __('fields.no_conversions') }}</h5>
                            <p class="text-muted">{{ __('fields.add_first_conversion') }}</p>
                            <a href="{{ route('unit-conversion.create') }}" class="btn btn-primary">
                                <i class="ti-plus"></i> {{ __('fields.add_conversion') }}
                            </a>
                        </div>
                    @endif
                </div>
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
            $('#datatable-buttons-conversion').DataTable({
                dom: 'Bfrtip',
                paging: false, // Disable DataTables pagination since we're using server-side pagination
                searching: false, // Disable DataTables search since we're using server-side search
                ordering: true, // Keep DataTables sorting for current page
                order: [], // Start with no default ordering
                info: false, // Hide DataTables info since we have custom pagination info
                buttons: [
                    {
                        extend: 'copy',
                        text: "کپی",
                        className: 'btn btn-outline-primary',
                        exportOptions: { columns: [6, 5, 4, 3, 2, 1, 0], modifier: { page: 'current' }, orthogonal: "rtlexport" }
                    },
                    {
                        extend: 'pdf',
                        text: 'pdf',
                        className: 'btn btn-outline-primary',
                        exportOptions: { columns: [6, 5, 4, 3, 2, 1, 0], modifier: { page: 'current' }, orthogonal: "rtlexport" },
                        customize: function (doc) {
                            doc.defaultStyle.font = "IRANSansWeb";
                            doc.content[1].table.widths = ['10%', '20%', '20%', '20%', '15%', '10%', '5%'];
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-primary',
                        exportOptions: { columns: [6, 5, 4, 3, 2, 1, 0], modifier: { page: 'current' } }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-primary',
                        exportOptions: { columns: [6, 5, 4, 3, 2, 1, 0], modifier: { page: 'current' } }
                    },
                    {
                        extend: 'print',
                        text: "پرینت",
                        className: 'btn btn-outline-primary',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6], modifier: { page: 'current' }, orthogonal: "rtlexport" }
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
                language: {
                    paginate: {
                        previous: "قبلی",
                        next: "بعدی"
                    }
                }
            });
        });
    </script>
@endsection