@extends('layouts.main')
@section('title', 'لیست فعالیت ها')
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
                    <h4 class="card-title mb-2">لیست فعالیت ها</h4>
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th colspan="6">
                                    {{-- to set active change btn-outline-dfprimary ==> btn-dfprimary --}}
                                    <a href="?action[]=Role" class="btn btn-outline-dfprimary shadow">Role<span> &#8595;&#8593;</span></a>
                                    <a href="?action[]=User" class="btn btn-outline-dfprimary shadow">User<span> &#8595;&#8593;</span></a>
                                </th>
                            </tr>
                            <tr>
                                <th>ردیف</th>
                                <th>کاربر انجام دهنده</th>
                                <th>مرجع فعالیت</th>
                                <th>نوع تغییر</th>
                                <th>زمان انجام</th>
                                <th>جزئیات</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @php($i = 1)
                            @foreach ($activities as $activity)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $activity->user->full_name ?? '-' }}</td>
                                    <td>{{ $activity->recordChange->model_detail['fa_name'] ?? '-' }}</td>
                                    <td>{{ $activity->action_persian_name }}</td>
                                    <td>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($activity->created_at)) }}</td>
                                    <td>
                                        @if($activity->user && $activity->recordChange)
                                            <a href="{{ route('activity.show', $activity) }}"><i class="ti-more-alt font-24"></i></a>
                                        @else
                                            <span class="text-muted">جزئیات در دسترس نیست</span>
                                        @endif
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
