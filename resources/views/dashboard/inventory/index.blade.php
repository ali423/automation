@extends('layouts.main')
@section('title', 'لیست موجودی انبار')
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
        <div class="col-xl-12 height-card box-margin">
            <div class="card">
                <div class="card-body">
                    <!-- Stacked Bar -->
                    <div id="panel-15" class="panel">
                        <h4 class="card-title">وضعیت انبار ها</h4>
                        <div class="row">
                            <div class="col-md-10">
                                <div id="mychart" class="row">
                                    @php($i = 1)
                                    @foreach ($warehouses as $warehouse)
                                        <div class="col mr-1 mt-5 mt-3">
                                            <span class="size">
                                                {{ number_format($warehouse->capacity) }} کیلوگرم
                                            </span>
                                            <div class="full-size">
                                                <span></span>
                                                <span
                                                    @switch($i) @case(1)
                                          class="current-size bg-primary"
                                        @break
                                        @case(2)
                                        class="current-size bg-success"
                                        @break
                                    @case(3)
                                    class="current-size bg-danger"
                                    @break
                                    @case(4)
                                    class="current-size bg-secondary"
                                    @break
                                    @case(5)
                                    class="current-size bg-warning"
                                    @php($i=0)
                                    @break @endswitch
                                                    data-current="{{ $warehouse->full_space_percentage }}"></span>
                                            </div>
                                            <span>
                                                {{ $warehouse->title }}
                                            </span>
                                        </div>
                                        @php($i++)
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-2">
                                <img src="{{ asset('img/ware-capacity/warechartex.png')}}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-2">لیست انبار ها</h4>
                    <table id="datatable-buttons-inventory" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ردیف</th>
                                <th> {{ __('fields.title') }}</th>
                                <th> {{ __('fields.type') }}</th>
                                <th> {{ __('fields.status') }}</th>
                                <th> {{ __('fields.capacity') }}</th>
                                <th> {{ __('fields.empty_space') }}</th>
                                <th>{{ __('fields.commodities_list') }}</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @php($i = 1)
                            @foreach ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $warehouse->title }}</td>
                                    <td>{{ __('fields.warehouse.types')[$warehouse->type] }}</td>
                                    <td>{{ __('fields.warehouse.status')[$warehouse->status] }}</td>
                                    <td>{{ number_format($warehouse->capacity) }}</td>
                                    <td>{{ number_format($warehouse->empty_space) }}</td>
                                    <td><a href="{{ route('inventory.show', $warehouse) }}" class=""><i
                                                class="ti-more-alt font-24"></i></a>
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
    {{-- chart --}}
    <script src="{{ asset('js/store-chart/store-chart.js') }}"></script>
    <script type="text/javascript">
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

            $('#datatable-buttons-inventory').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: "کپی",
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [5, 4, 3, 2, 1, 0],
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
                            columns: [5, 4, 3, 2, 1, 0],
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
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [5, 4, 3, 2, 1, 0],
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-primary',
                        exportOptions: {
                            columns: [5, 4, 3, 2, 1, 0],
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
                            columns: [0, 1, 2, 3, 4, 5],
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
        });
    </script>
@endsection
