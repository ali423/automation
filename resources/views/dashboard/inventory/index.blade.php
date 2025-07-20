@extends('layouts.main')
@section('title', 'لیست موجودی ها')
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
                    <h4 class="card-title mb-2">لیست موجودی ها</h4>
                    <table id="datatable-buttons-inventory" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                        <tr>
                            <th>ردیف</th>
                            <th>کالا</th>
                            <th>واحد</th>
                            <th>مقدار موجودی</th>
                            <th>قیمت خرید</th>
                            <th>قیمت فروش</th>
                            <th>وضعیت</th>
                            <th>{{ __('fields.details') }}</th>
                        </tr>
                        </thead>

                        <tbody class="text-center">
                        @if($inventories->count() > 0)
                            @php($i = 1)
                            @foreach ($inventories as $inventory)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $inventory->commodity->title ?? 'نامشخص' }}</td>
                                    <td>{{ $inventory->unit->name ?? 'نامشخص' }}</td>
                                    <td>{{ number_format($inventory->amount, 2) }}</td>
                                    <td>{{ number_format($inventory->purchase_price ?? 0) }} تومان</td>
                                    <td>{{ number_format($inventory->sale_price ?? 0) }} تومان</td>
                                    <td>
                                        @if($inventory->active)
                                            <span class="badge badge-success">فعال</span>
                                        @else
                                            <span class="badge badge-danger">غیرفعال</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('inventory.show', $inventory) }}" class=""><i
                                                class="ti-more-alt font-24"></i></a>
                                    </td>
                                </tr>
                                @php($i++)
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="ti-info-alt"></i>
                                        {{ $message ?? 'هیچ موجودی یافت نشد.' }}
                                    </div>
                                </td>
                            </tr>
                        @endif
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

            $('#datatable-buttons-inventory').DataTable({
                dom: 'Bfrtip',
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
                            doc.content[1].table.widths = ['20%', '20%', '20%', '20%', '20%', '20%',
                                '20%'
                            ];
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