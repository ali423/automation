@extends('layouts.main')
@section('title', 'لیست درخواست های تولید')
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
                    <h4 class="card-title mb-2">لیست درخواست های تولید</h4>
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ردیف</th>
                                <th>{{ __('fields.status') }}</th>
                                <th>{{ __('fields.production-request.number') }}</th>
                                <th>محصول تولیدی</th>
                                <th>مقدار تولید</th>
                                <th>{{ __('fields.production-request.total_cost') }}</th>
                                <th>{{ __('fields.production-request.profit') }}</th>
                                <th>{{ __('fields.created_at') }}</th>
                                <th>{{ __('fields.creator') }}</th>
                                <th>{{ __('fields.details') }}</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @php $i = 1; @endphp
                            @foreach ($requests as $request)
                                @php
                                    $mainProduct = $request->outputProducts->first();
                                @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $request->status_text }}</td>
                                    <td>{{ $request->number }}</td>
                                    <td>{{ $mainProduct ? $mainProduct->title : 'نامشخص' }}</td>
                                    <td>
                                        @if($mainProduct)
                                            {{ number_format($mainProduct->pivot->amount) }} 
                                            {{ $mainProduct->unit ? $mainProduct->unit->name : '' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($request->total_cost) }}</td>
                                    <td>{{ number_format($request->profit) }}</td>
                                    <td>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}</td>
                                    @if(isset($request->creator_user))
                                        <td>{{ $request->creator_user->full_name }}</td>
                                    @else
                                        <td>سیستم</td>
                                    @endif
                                    <td><a href="{{ route('production-request.show', $request) }}" class=""><i class="ti-more-alt font-24"></i></a></td>
                                </tr>
                                @php $i++; @endphp
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
    <script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-button.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.html5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.flash.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.print.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatables-keytable.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatables-select.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#datatable-buttons').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            });
        });
    </script>
@endsection 