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
                            <div class="form-row m-3">
                                <div class="form-group col">
                                    <label for="customer_id">نام مشتری</label>
                                    <select id="customer_id" class="form-control" name="customer_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{$customer->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                       مشتری را انتخاب کنید
                                    </div>
                                </div>
                            </div>
                            <div id="product_formul" class="col-lg-12">
                                <p>اطلاعات کالا</p>
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    <div class="form-group col-md-4">
                                        <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
                                        <select id="commodity_id" class="form-control" name="commodity_id[0]" onchange="commodity_change(this)" required>
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
                                        <select id="unit" class="form-control" name="unit[0]" onchange="unitchange(this)" required>
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
                                        <label for="price"> {{ __('fields.sell-price') }}</label>
                                        <input type="text" id="price" name="price[0]" class="form-control"
                                               placeholder="{{ __('fields.sell-price') }}" required>
                                        <div class="invalid-feedback">{{ __('fields.sell-price') }} را انتخاب کنید</div>
                                    </div>

                                    <div class="warehouse-inputs position-relative" style="overflow: hidden;height:0;width:0;">
                                        <input type="text" name="warehouse_id[1][0]" value="1">
                                        <input type="text" name="warehouse_id[2][1]" value="2">
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
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"> <div class="form-group col-md-4"> <label for="commodity_id"> {{ __('fields.commodity.name') }}</label> <select id="commodity_id" class="form-control" name="commodity_id[]" onchange="commodity_change(this)" required> <option value="">انتخاب کنید</option>@foreach ($commodities as $commodity)<option value="{{ $commodity->id }}">{{ $commodity->title }}</option>@endforeach</select> <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div> </div><div class="form-group col-md-2"><label for="unit"> {{ __('fields.unit') }}</label><select id="unit" class="form-control" name="unit[0]" onchange="unitchange(this)" required><option value="">انتخاب کنید...</option>@foreach( __('fields.commodity.units') as $key=>$value)<option value="{{$key}}">{{$value}}</option>@endforeach</select><div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div></div><div class="form-group col-md-2"><label for="total">مجموع</label><input type="number" value="0" id="total-amount" name="totalamount[0]" class="form-control"placeholder="{{ __('fields.sell-price') }}" required disabled></div><div class="form-group col-md-4"><label for="price"> {{ __('fields.sell-price') }}</label><input type="text" id="price" name="price[0]" class="form-control"placeholder="{{ __('fields.sell-price') }}" required><div class="invalid-feedback">{{ __('fields.sell-price') }} را انتخاب کنید</div></div><i id="removeRow" type="submit" class="ti-close"></i></div></div>';
            $('#newRow').append(html);
            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id[' + index + ']');
                element.querySelector('#price').setAttribute('name', 'price[' + index + ']');
                element.querySelector('#unit').setAttribute('name', 'unit[' + index + ']');
                element.querySelector('#total-amount').setAttribute('name', 'totalamount[' + index + ']');
            });
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).closest('#inputFormRow').remove();
            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id[' + index + ']');
                element.querySelector('#price').setAttribute('name', 'price[' + index + ']');
                element.querySelector('#unit').setAttribute('name', 'unit[' + index + ']');
                element.querySelector('#total-amount').setAttribute('name', 'totalamount[' + index + ']');
            });
        });

        function commodity_change(e){
            var commodity_id = e.value;
            var thisForm = e.closest('#inputFormRow');
            thisForm.querySelectorAll('.wares').forEach(element => {
                element.remove();
            });
            $.ajax({
                url: '/inventory-ajax/' + commodity_id,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    var price = response['price'];
                    var warehouses = response['warehouses'];
                    var result = e.name.split('[');
                    var result2 = result[1].split(']');
                    $('input[name="price['+result2[0]+']"]').val(price);
                    warehouses.forEach((ware,index)=>{
                        var wareTemplate='<div class="input-group mb-3 wares"><div class="input-group-prepend"><span class="input-group-text" id="'+ware['title']+'">'+ware['title']+'</span></div><input type="number" class="ware-amount form-control" min="0" max="'+ware['amount']+'" value="0" name="amount['+commodity_id+']['+ware['id']+']" onkeyup="total(this,this.value)" required><div class="input-group-append"><span class="input-group-text" id="ware-amount">'+'حداکثر: '+ware['amount']+'</span></div><div class="warehouse-inputs position-relative" style="overflow: hidden;height:0;width:0;"><input type="text" name="warehouse_id['+commodity_id+']['+index+']" value="'+ware['id']+'"></div></div>';
                        thisForm.insertAdjacentHTML('beforeend', wareTemplate);
                    })
                }
            });
        }
        function total(e,value){
            var unit = e.closest('#inputFormRow').querySelector('#unit');
            var wareamount = e.closest('#inputFormRow').querySelectorAll('.ware-amount');
            var total = 0;
            wareamount.forEach(element=>{
                total = total + parseFloat(element.value);
            })
            e.closest('#inputFormRow').querySelector('#total-amount').value = total.toFixed(2);
            // switch (unit.value) {
            //     case "kg":
            //         wareamount.forEach(element=>{
            //             total = total + parseFloat(element.value);
            //         })
            //         e.closest('#inputFormRow').querySelector('#total-amount').value = total.toFixed(2);
            //         break;
            //     case "keg":
            //         wareamount.forEach(element=>{
            //             total = total + parseFloat(element.value/185);
            //         })
            //         e.closest('#inputFormRow').querySelector('#total-amount').value = total.toFixed(2);
            //         break;
            //     case "twenty_liters":
            //         wareamount.forEach(element=>{
            //             total = total + parseFloat(element.value/17.8);
            //         })
            //         e.closest('#inputFormRow').querySelector('#total-amount').value = total.toFixed(2);
            //         break;
            //
            //     default:
            //         wareamount.forEach(element=>{
            //             total = total + parseFloat(element.value);
            //         })
            //         e.closest('#inputFormRow').querySelector('#total-amount').value = total.toFixed(2);
            //         break;
            // }
        }

        function unitchange(e){
            e.closest('#inputFormRow').querySelectorAll('.ware-amount').forEach(element=>{
                element.value = 0;
                e.closest('#inputFormRow').querySelector('#total-amount').value = 0;
            })
        }
    </script>


    <!-- These plugins only need for the run this page -->

    <script src="{{ asset('js/default-assets/active.js') }}"></script>

    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{asset('js/default-assets/file-upload.js')}}"></script>


@endsection

