@extends('layouts.main')
@section('title', 'لیست گزارش خرید کالا')
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
        <div class="col-sm-12 col-xl-12">
            <div class="card box-margin">
                <div class="card-body">
                    <div class="float-right"><i class="fa fa-codiepie text-warning font-60"></i></div><span class="badge badge-warning"> کالای تحت گذارش : {{ $requests['title'] }}  </span>
                    <h4 class="my-3"> میانگین قیمت خرید هر
                        <select id="unit">
                            <option value="kg">کیلوگرم</option>
                            <option value="20lit">گالن 20 لیتری</option>
                            <option value="keg">بشکه</option>
                        </select>
                        {{ $requests['title'] }}
                  <span id="price">{{number_format($requests['avr_price'])}}</span>
                        ریال می باشد
                    </h4>
                    <p class="mb-0"><span class="text-danger"></span>بازه زمانی {{ $requests['date_from'] }} الی {{ $requests['date_to'] }}</p>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-2">لیست گزارش خرید کالا</h4>
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ردیف</th>
                                <th> {{ __('fields.commodity.name') }}</th>
                                <th> {{ __('fields.commodity.amount') }}</th>
                                <th> {{ __('fields.purchase_unit_price') }}</th>
                                <th> {{ __('fields.unit') }}</th>
                                <th>{{ __('fields.created_at') }}</th>
                                <th>{{ __('fields.details') }}</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @php($i = 1)
                            @foreach ($requests['requests'] as $request)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{$requests['title']  }}</td>
                                    <td>{{$request['amount']  }}</td>
                                    <td>{{$request['product_purchase_price']  }}</td>
                                    <td>{{__('fields.commodity.units')[$request['unit']]  }}</td>
                                    <td>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request['created_at'] )) }}
                                    </td>
                                    <td><a href="{{ route('importing-request.show', $request['request_id'] ) }}" class=""><i class="ti-more-alt font-24"></i></a>
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

    <script>
        $(document).ready(function() {
            var kg_price={{$requests['avr_price']}};
            var price=0;
            $("#unit").change(function() {
                var unit = $("#unit option:selected").val();
                switch (unit) {
                    case "kg":
                        price = kg_price;
                        break;
                    case "20lit":
                        price = Math.round(kg_price*17.8);
                        break;
                    case "keg":
                        price =Math.round(kg_price*185);
                        break;
                }
                $('#price').text(price);
            });
        });
    </script>

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
