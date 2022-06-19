@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ثبت درخواست ورود کالا به انبار</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('importing-request.store') }}" class="needs-validation forms-sample" enctype="multipart/form-data" novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputState"> {{ __('fields.commodity.name') }}</label>
                                    <select id="inputState" class="form-control" name="commodity_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($commodities as $commodity)
                                            <option value="{{ $commodity->id }}"
                                                    @if (old('commodity_id') == $commodity->id) selected @endif>{{ $commodity->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="type"> {{ __('fields.unit') }}</label>
                                    <select id="type" class="form-control" name="unit" required>
                                        <option value="">انتخاب کنید...</option>
                                        @foreach( __('fields.commodity.units') as $key=>$value)
                                            <option value="{{$key}}"
                                                    @if (old('unit') == $key) selected @endif
                                            >{{$value}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputState"> {{ __('fields.warehouse.name') }}</label>
                                    <select id="inputState" class="form-control" name="warehouse_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}"
                                                    @if (old('warehouse_id') == $warehouse->id) selected @endif>{{ $warehouse->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">{{ __('fields.warehouse.name') }} را انتخاب کنید.</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="capacity"> {{  __('fields.commodity.amount') }}</label>
                                    <input type="number" min="1" name="amount" value="{{ old('amount') }}" class="form-control"
                                           id="capacity" autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]"  required="">
                                    <div class="invalid-feedback">
                                        لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                                    <button class="file-upload-browse btn btn-primary" type="button">انتخاب فایل</button>
                                                </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">ثبت درخواست</button>
                            <a href="{{ route('importing-request.index') }}" class="btn btn-danger">انصراف</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->

    <script src="{{ asset('js/default-assets/active.js') }}"></script>

    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{asset('js/default-assets/file-upload.js')}}"></script>


@endsection

