@extends('layouts.main')
@section('title', 'لیست نقش ها')
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
                    <h4 class="card-title mb-2">لیست نقش ها</h4>
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ردیف</th>
                                <th> {{ __('fields.name') }}</th>
                                <th> {{ __('fields.title') }}</th>
                                <th>{{ __('fields.created_at') }}</th>
                                <th>{{ __('fields.creator') }}</th>
                                <th>{{ __('fields.details') }}</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @php($i = 1)
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->title }}</td>
                                    <td>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($role->created_at)) }}
                                    </td>
                                    @if(isset($role->creator_user))
                                    <td>{{ $role->creator_user->name.' '. $role->creator_user->lastname }}</td>
                                    @else
                                        <td>سیستم</td>
                                    @endif
                                    <td><a href="{{ route('role.show', $role) }}" class=""><i class="ti-more-alt font-24"></i></a>
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
    <script src="{{ asset('js/default-assets/customDataTable.js') }}"></script>

@endsection
