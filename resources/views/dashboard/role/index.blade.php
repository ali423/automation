@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')
    <!-- These plugins only need for the run this page -->
    <link rel="stylesheet" href="{{ asset('css/default-assets/datatables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/select.bootstrap4.css') }}">
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
                            <th>نام</th>
                            <th>عنوان</th>
                            <th>تاریخ ایجاد</th>
                            <th>ایجاد کننده</th>
                            <th>جزئیات</th>
                        </tr>
                        </thead>

                        <tbody class="text-center">
                        @php($i=1)
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->title }}</td>
                            <td>{{\Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($role->created_at))}}</td>
                            <td>ادمین</td>
                            <td><a href="{{route('role.show',$role)}}"><i class="fa fa-object-group"></i></a></td>
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
        <!-- These plugins only need for the run this page -->
        <script src="{{asset('js/default-assets/jquery.datatables.min.js')}}"></script>
        <script src="{{asset('js/default-assets/datatables.bootstrap4.js')}}"></script>
        <script src="{{asset('js/default-assets/datatable-responsive.min.js')}}"></script>
        <script src="{{asset('js/default-assets/responsive.bootstrap4.min.js')}}"></script>
        <script src="{{asset('js/default-assets/datatable-button.min.js')}}"></script>
        <script src="{{asset('js/default-assets/button.bootstrap4.min.js')}}"></script>
        <script src="{{asset('js/default-assets/button.html5.min.js')}}"></script>
        <script src="{{asset('js/default-assets/button.flash.min.js')}}"></script>
        <script src="{{asset('js/default-assets/button.print.min.js')}}"></script>
        <script src="{{asset('js/default-assets/datatables.keytable.min.js')}}"></script>
        <script src="{{asset('js/default-assets/datatables.select.min.js')}}"></script>
        <script src="{{asset('js/default-assets/demo.datatable-init.js')}}"></script>
@endsection
