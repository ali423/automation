@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        @if($commodities->count() > 0)
        <div class="col-xl-12 height-card box-margin">
            <div class="card">
                <div class="card-body">
                    <!-- Pie Chart -->
                    <div id="panel-8" class="panel d-flex flex-column align-items-center">
                        <h4 class="card-title">کالا های موجود در انبار {{ $warehouse->title }}</h4>
                        <div class="panel-container show">
                            <div class="panel-content">
                                <div id="piechart" class="ct-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">کالا های موجود در انبار {{ $warehouse->title }}</h4>
                @foreach ($commodities as $commodity)
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-4">
                                    <label for="fields"> {{ __('fields.commodity.name') }}</label>
                                    <input type="text" name="fields" value="{{ $commodity->title }}"
                                        class="form-control" id="exampleInputEmail111" autocomplete="off" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail111">
                                        {{ __('fields.commodity.warehouse_amount') }}</label>
                                    <input type="text" name="type"
                                        value="{{ number_format($commodity->pivot->commodity_amount) }}"
                                        class="form-control" id="exampleInputEmail111" autocomplete="off" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail111">&nbsp;</label>
                                    <a href="{{ route('inventory.edit', [
                                        'commodity' => $commodity->id,
                                        'warehouse' => $warehouse->id,
                                    ]) }}"
                                        class="form-control btn btn-primary">اصلاح موجودی</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    {{-- chart --}}
    <script src="{{ asset('js/store-chart/store-chart.js') }}"></script>
    <script src="{{ asset('js/default-assets/apexchart.min.js') }}"></script>
    <script type="text/javascript">
        var options = {

          series:  @json(array_column(array_column($commodities->toArray(),'pivot'),'commodity_amount')),
          chart: {
          width: 380,
          type: 'pie',
        },
        labels: @json(array_column($commodities->toArray(),'title')),
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#piechart"), options);
        chart.render();
    </script>
@endsection
