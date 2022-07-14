@extends('layouts.main')
@section('title','درخواست ورود کالا به انبار')

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
                            <div id="product_formul" class="col-lg-12">
                                <p>اطلاعات ورود کالا به انبار</p>
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    <div class="form-group col-md-6">
                                        <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
                                        <select id="commodity_id" class="form-control" name="commodity_id[0]" required>
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($commodities as $commodity)
                                                <option value="{{ $commodity->id }}">{{ $commodity->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="unit"> {{ __('fields.unit') }}</label>
                                        <select id="unit" class="form-control" name="unit[0]" required>
                                            <option value="">انتخاب کنید...</option>
                                            @foreach( __('fields.commodity.units') as $key=>$value)
                                                <option value="{{$key}}"
                                                >{{$value}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="warehouse_id"> {{ __('fields.warehouse.name') }}</label>
                                        <select id="warehouse_id" class="form-control" name="warehouse_id[0]" required>
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.warehouse.name') }} را انتخاب کنید.</div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                                        <input type="number" id="amount" min="1" name="amount[0]" class="form-control"
                                               autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]"  required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                                        </div>
                                    </div>
                                </div>

                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                            </div>

                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default" accept="image/*,.pdf,.zip,.rar">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">انتخاب فایل</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-20">
                                <label for="comment">توضیحات</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment" rows="6">{{old('comment')}}</textarea>
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

    <script type="text/javascript">
        // add row
        $("#addRow").click(function() {
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"> <div class="form-group col-md-6"> <label for="commodity_id"> {{ __('fields.commodity.name') }}</label> <select id="commodity_id" class="form-control" name="commodity_id[]" required> <option value="">انتخاب کنید</option>@foreach ($commodities as $commodity)<option value="{{ $commodity->id }}">{{ $commodity->title }}</option>@endforeach</select> <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div> </div> <div class="form-group col-md-6"> <label for="unit"> {{ __('fields.unit') }}</label> <select id="unit" class="form-control" name="unit[]" required> <option value="">انتخاب کنید...</option>@foreach( __('fields.commodity.units') as $key=>$value)<option value="{{$key}}">{{$value}}</option>@endforeach</select> <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div> </div> <div class="form-group col-md-6"> <label for="warehouse_id"> {{ __('fields.warehouse.name') }}</label> <select id="warehouse_id" class="form-control" name="warehouse_id[]" required> <option value="">انتخاب کنید</option>@foreach ($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->title }}</option>@endforeach</select> <div class="invalid-feedback">{{ __('fields.warehouse.name') }} را انتخاب کنید.</div> </div> <div class="form-group col-md-6"> <label for="amount"> {{  __('fields.commodity.amount') }}</label> <input type="number" min="1" name="amount[]" class="form-control"id="amount" autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]"  required=""> <div class="invalid-feedback">لطفاً {{  __('fields.commodity.amount') }} را وارد کنید. </div></div> <i id="removeRow" type="submit" class="ti-close"></i></div></div>';
            $('#newRow').append(html);
            document.querySelectorAll('#inputFormRow').forEach((element,index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id['+index+']');
                element.querySelector('#unit').setAttribute('name', 'unit['+index+']');
                element.querySelector('#warehouse_id').setAttribute('name', 'warehouse_id['+index+']');
                element.querySelector('#amount').setAttribute('name', 'amount['+index+']');
            });
        });

        // remove row
        $(document).on('click', '#removeRow', function() {
            $(this).closest('#inputFormRow').remove();
            document.querySelectorAll('#inputFormRow').forEach((element,index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id['+index+']');
                element.querySelector('#unit').setAttribute('name', 'unit['+index+']');
                element.querySelector('#warehouse_id').setAttribute('name', 'warehouse_id['+index+']');
                element.querySelector('#amount').setAttribute('name', 'amount['+index+']');
            });
        });
    </script>
    <!-- These plugins only need for the run this page -->

    <script src="{{ asset('js/default-assets/active.js') }}"></script>

    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{asset('js/default-assets/file-upload.js')}}"></script>


@endsection

