@extends('layouts.main')
@section('title', 'تحویل سفارش')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">تحویل سفارش</h4>

                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('order-confirm.store',$order) }}"
                            class="needs-validation forms-sample" enctype="multipart/form-data" novalidate="">
                            @csrf
                            <div class="form-row m-3">
                                <div class="form-group col">
                                    <label for="customer_id">نام مشتری</label>
                                    <select id="customer_id" class="form-control" name="customer_id" required>
                                        <option value="{{ $order->customer_id }}">{{ $order->customer->name }}</option>
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
                                        <select id="commodity_id" class="form-control" name="commodity_id[0]"
                                            onchange="commodity_change(this)" required>
                                            <option value="{{ $order->commodity_id }}">{{ $order->commodity->title }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب
                                            کنید.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="unit"> {{ __('fields.unit') }}</label>
                                        <select id="unit" class="form-control" name="unit[0]"
                                            onchange="unitchange(this)" required>
                                            <option value="{{ $order->unit }}">
                                                {{ __('fields.commodity.units')[$order->unit] }}</option>
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="total">مجموع</label>
                                        <input type="number" value="0" id="total-amount" name="totalamount[0]"
                                            class="form-control" placeholder="{{ __('fields.sell-price') }}" required
                                            disabled>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="price"> {{ __('fields.sell-price') }}</label>
                                        <input type="text" id="price" value="{{ $order->price }}" name="price[0]"
                                            class="form-control" placeholder="{{ __('fields.sell-price') }}" required>
                                        <div class="invalid-feedback">{{ __('fields.sell-price') }} را انتخاب کنید</div>
                                    </div>
                                    @php($fixed_amount = $order->commodity_amount)
                                    @foreach ($order->commodity->warehouses()->orderBy('commodity_amount', 'DESC')->get() as $warehouse)
                                        @switch ($order->unit)
                                            @case ('keg')
                                                @php($warehouse_max = round($warehouse->pivot->commodity_amount * 185, 2))
                                            @break

                                            @case ('kg')
                                                @php($warehouse_max = $warehouse->pivot->commodity_amount)
                                            @break

                                            @case ('twenty_liters')
                                                @php($warehouse_max = round($warehouse->pivot->commodity_amount * 17.8, 2))
                                            @break
                                        @endswitch
                                        <div class="input-group mb-3 wares">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    id="انبار مرکزی">{{ $warehouse->title }}</span>
                                            </div>
                                            <input id="commodityamount" onkeyup="commodityamountfunc(this)" type="number" class="ware-amount form-control" min="0"
                                                max="{{ $warehouse_max }}"
                                                @if ($fixed_amount <= $warehouse_max) value="{{ $fixed_amount }}"
                                                   @else
                                                   @php($fixed_amount = $fixed_amount- $warehouse_max)
                                                   value="{{ $warehouse_max }}" @endif
                                                name="amount[{{ $order->commodity_id }}][{{ $warehouse->id }}]"
                                                required="">
                                            <div class="input-group-append"><span class="input-group-text"
                                                    id="ware-amount">حداکثر:{{ $warehouse_max }}</span>
                                            </div>
                                            <div class="warehouse-inputs position-relative"
                                                style="overflow: hidden;height:0;width:0;">
                                                <input type="text" name="warehouse_id[{{ $order->commodity_id }}][]"
                                                    value="{{ $warehouse->id }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default"
                                    accept="image/*,.pdf,.zip,.rar">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled=""
                                        placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">انتخاب
                                            فایل</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-20">
                                <label for="comment">توضیحات</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment" rows="6">{{ old('comment') }}</textarea>
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


    <!-- These plugins only need for the run this page -->

    <script src="{{ asset('js/default-assets/active.js') }}"></script>

    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/default-assets/file-upload.js') }}"></script>
    <script type="text/javascript">
        var inputformrow = document.querySelectorAll('#inputFormRow');
        inputformrow.forEach(element => {
            var total = 0;
            element.querySelectorAll('#commodityamount').forEach(input => {
               total = total + input.value;
            });
            element.querySelector('#total-amount').value = parseFloat(total).toFixed(2);
        });

        function commodityamountfunc(e){
            var formrow = e.closest('#inputFormRow');
            var totalamount = formrow.querySelector('#total-amount');
            var total = 0;
            formrow.querySelectorAll('#commodityamount').forEach(element => {
                total = total + element.value;
                totalamount.value = total;
            });
        }
    </script>

@endsection
