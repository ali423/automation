@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ثبت درخواست فروش کالا</h4>

                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('withdrawal-request.store') }}"
                              class="needs-validation forms-sample" enctype="multipart/form-data" novalidate="">
                            @csrf
                            <div id="product_formul" class="col-lg-12">
                                <p>اطلاعات کالا</p>
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    <div class="form-group col-md-4">
                                        <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
                                        <select id="commodity_id" class="form-control" name="commodity_id[0]" required>
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($commodities as $commodity)
                                                <option value="{{ $commodity->id }}">{{ $commodity->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب
                                            کنید.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
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
                                    <div class="form-group col-md-2">
                                        <label for="total">مجموع</label>
                                        <input type="number" value="0" id="total-amount" name="totalamount[0]" class="form-control"
                                        placeholder="{{ __('fields.sell-price') }}" required disabled>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="sell-price"> {{ __('fields.sell-price') }}</label>
                                        <input type="text" id="sell-price" name="sell-price[0]" class="form-control"
                                               placeholder="{{ __('fields.sell-price') }}" required>
                                        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                    </div>
                                </div>

                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                            </div>

                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default"
                                       accept="image/*,.pdf,.zip,.rar">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled=""
                                           placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary"
                                                type="button">انتخاب فایل</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-20">
                                <label for="comment">توضیحات</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment"
                                          rows="6">{{old('comment')}}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">ثبت درخواست</button>
                            <a href="{{ route('withdrawal-request.index') }}" class="btn btn-danger">انصراف</a>
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
        $("#addRow").click(function () {
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"> <div class="form-group col-md-4"> <label for="commodity_id"> {{ __('fields.commodity.name') }}</label> <select id="commodity_id" class="form-control" name="commodity_id[]" required> <option value="">انتخاب کنید</option>@foreach ($commodities as $commodity)<option value="{{ $commodity->id }}">{{ $commodity->title }}</option>@endforeach</select> <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div> </div><div class="form-group col-md-2"><label for="unit"> {{ __('fields.unit') }}</label><select id="unit" class="form-control" name="unit[0]" required><option value="">انتخاب کنید...</option>@foreach( __('fields.commodity.units') as $key=>$value)<option value="{{$key}}">{{$value}}</option>@endforeach</select><div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div></div><div class="form-group col-md-2"><label for="total">مجموع</label><input type="number" value="0" id="total-amount" name="totalamount[0]" class="form-control"placeholder="{{ __('fields.sell-price') }}" required disabled></div><div class="form-group col-md-4"><label for="sell-price"> {{ __('fields.sell-price') }}</label><input type="text" id="sell-price" name="sell-price[0]" class="form-control"placeholder="{{ __('fields.sell-price') }}" required><div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div></div><i id="removeRow" type="submit" class="ti-close"></i></div></div>';
            $('#newRow').append(html);
            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id[' + index + ']');
                element.querySelector('#sell-price').setAttribute('name', 'sell-price[' + index + ']');
                element.querySelector('#unit').setAttribute('name', 'unit[' + index + ']');
                element.querySelector('#total-amount').setAttribute('name', 'totalamount[' + index + ']');
            });
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).closest('#inputFormRow').remove();
            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id[' + index + ']');
                element.querySelector('#sell-price').setAttribute('name', 'sell-price[' + index + ']');
                element.querySelector('#unit').setAttribute('name', 'unit[' + index + ']');
                element.querySelector('#total-amount').setAttribute('name', 'totalamount[' + index + ']');
            });
        });
    </script>

    <script type='text/javascript'>
        $(document).ready(function () {
            let commodities = document.querySelectorAll('#commodity_id');
            changecom(commodities);
            function changecom(commodities){
                commodities.forEach(element=>{
                    // console.log(element);
                    element.addEventListener('change',function () {
                        // Department id
                        var id = $(this).val();
                        let thisForm = this.closest('#inputFormRow');
                        thisForm.querySelectorAll('.wares').forEach(element => {
                            element.remove();
                        });
                        // AJAX request
                        $.ajax({
                            url: '/inventory-ajax/' + id,
                            type: 'get',
                            dataType: 'json',
                            success: function (response) {
                                var price = response['price'];
                                var warehouses = response['warehouses'];
                                // alert(warehouses['0']['title']);
                                // alert(warehouses['0']['id']);
                                // alert(warehouses['0']['amount']);
                                warehouses.forEach((ware,index)=>{
                                    var wareTemplate='<div class="input-group mb-3 wares"><div class="input-group-prepend"><span class="input-group-text" id="'+ware['title']+'">'+ware['title']+'</span></div><input type="number" class="form-control" min="0" max="'+ware['amount']+'" name="'+ware['id']+'"><div class="input-group-append"><span class="input-group-text" id="ware-amount">'+'max: '+ware['amount']+'</span></div></div>';
                                    thisForm.insertAdjacentHTML('beforeend', wareTemplate);
                                })
                            }
                        });
                    });
                })
            }
        });
    </script>

    <!-- These plugins only need for the run this page -->

    <script src="{{ asset('js/default-assets/active.js') }}"></script>

    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{asset('js/default-assets/file-upload.js')}}"></script>


@endsection

