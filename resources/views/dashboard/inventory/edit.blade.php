@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش موجودی کالا {{ $commodity->title  }} در انبار {{$warehouse->title}}</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('inventory.update') }}" class="needs-validation"
                            novalidate="">
                            @method('PATCH')
                            @csrf
                            <div class="form-row">
                                <div class="form-row col-md-12">
                                    <div class="form-group col-md-4">
                                        <label for="fields"> {{ __('fields.commodity.name') }}</label>
                                        <input type="text" name="" value="{{ $commodity->title }}" class="form-control"
                                               id="exampleInputEmail111" autocomplete="off" disabled
                                               >
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="exampleInputEmail111"> {{ __('fields.commodity.warehouse_amount') }}</label>
                                        <input type="text" name="commodity_amount" value="{{ $commodity->pivot->commodity_amount  }}" class="form-control"
                                               id="exampleInputEmail111" autocomplete="off"
                                               >
                                    </div>
                                    <input type="text" class="d-none" name="commodity" value="{{$commodity->id}}">
                                    <input type="text" class="d-none" name="warehouse" value="{{$warehouse->id}}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ویرایش</button>
                            <a href="{{ route('inventory.index') }}" class="btn btn-danger">انصراف</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection

