@extends('layouts.main')
@section('title', 'داشبورد')
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
                    <h4 class="card-title mb-2">لیست درخواست فروش کالا</h4>
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ردیف</th>
                                <th> {{ __('fields.status') }}</th>
                                <th> {{ __('fields.withdrawal-request.number') }}</th>
                                <th> {{ __('fields.customer') }}</th>
                                <th> {{ __('fields.total_price') }}</th>
                                <th>{{ __('fields.created_at') }}</th>
                                <th>{{ __('fields.creator') }}</th>
                                <th>{{ __('fields.details') }}</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @php($i = 1)
                            @foreach ($requests as $request)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{__('fields.withdrawal-request.status')[$request->status]  }}</td>
                                    <td>{{$request->number }}</td>
                                    <td>{{$request->customer->name }}</td>
                                    <td>{{ number_format($request->total_price['number']) }}</td>
                                    <td>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}
                                    </td>
                                    @if(isset($request->creator_user))
                                    <td>{{ $request->creator_user->full_name }}</td>
                                    @else
                                        <td>سیستم</td>
                                    @endif
                                    <td><a href="{{ route('withdrawal-request.show', $request) }}" class=""><i class="ti-more-alt font-24"></i></a>
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

    $('#datatable-buttons').DataTable({
        dom: 'Bfrtip',
        buttons: [{
                extend: 'copy',
                text: "کپی",
                className: 'btn btn-outline-primary',
                exportOptions: {
                    columns: [6,5,4, 3, 2, 1, 0],
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
                    columns: [6,5,4, 3, 2, 1, 0],
                    modifier: {
                        page: 'current'
                    },
                    orthogonal: "rtlexport"
                },
                customize: function(doc) {
                    doc.defaultStyle.font = "IRANSansWeb";
                    doc.content[1].table.widths = ['20%','20%','20%', '20%', '20%', '20%', '20%'];
                    doc.styles.tableBodyEven.alignment = 'center';
                    doc.styles.tableBodyOdd.alignment = 'center';
                }
            },
            {
                extend: 'excel',
                className: 'btn btn-outline-primary',
                exportOptions: {
                    columns: [6,5,4, 3, 2, 1, 0],
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'csv',
                className: 'btn btn-outline-primary',
                exportOptions: {
                    columns: [6,5,4, 3, 2, 1, 0],
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
                    columns: [0, 1, 2, 3, 4,5,6],
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
