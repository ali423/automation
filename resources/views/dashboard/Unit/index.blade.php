@extends('layouts.main')
@section('title', 'لیست واحدها')
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
                        <h4 class="card-title mb-0">لیست واحدها</h4>
                        <a href="{{ route('unit.create') }}" class="btn btn-primary">
                            <i class="ti-plus mr-1"></i> افزودن واحد جدید
                        </a>
                    </div>
                    
                    <table id="datatable-buttons-unit" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                        <tr>
                            <th>ردیف</th>
                            <th>نام واحد</th>
                            <th>نماد</th>
                            <th>تاریخ ایجاد</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>

                        <tbody class="text-center">
                        @php($i = 1)
                        @foreach ($units as $unit)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $unit->name }}</td>
                                <td>{{ $unit->symbol }}</td>
                                <td>{{ jdate($unit->created_at)->format('Y/m/d') }}</td>
                                <td class="d-flex justify-content-center">
                                    <a href="{{ route('unit.edit', $unit) }}" class="btn btn-sm btn-warning mr-1" 
                                       data-toggle="tooltip" title="ویرایش">
                                        <i class="ti-pencil"></i>
                                    </a>
                                    <form action="{{ route('unit.destroy', $unit) }}" method="POST" 
                                          onsubmit="return confirm('آیا از حذف این واحد اطمینان دارید؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                data-toggle="tooltip" title="حذف">
                                            <i class="ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach
                        </tbody>
                    </table>

                </div> <!-- end card body-->
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

            $('#datatable-buttons-unit').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'copy',
                    text: "کپی",
                    className: 'btn btn-outline-primary',
                    exportOptions: {
                        columns: [4, 3, 2, 1, 0],
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
                            columns: [4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            },
                            orthogonal: "rtlexport"
                        },
                        customize: function (doc) {
                            doc.defaultStyle.font = "IRANSansWeb";
                            doc.content[1].table.widths = ['20%', '20%', '20%', '20%', '20%'];
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [4, 3, 2, 1, 0],
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
                            columns: [0, 1, 2, 3, 4],
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
                    },
                    "search": "جستجو:",
                    "lengthMenu": "نمایش _MENU_ رکورد در صفحه",
                    "zeroRecords": "هیچ رکوردی یافت نشد",
                    "info": "نمایش صفحه _PAGE_ از _PAGES_",
                    "infoEmpty": "هیچ رکوردی موجود نیست",
                    "infoFiltered": "(فیلتر شده از _MAX_ رکورد)"
                }
            });
            
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection