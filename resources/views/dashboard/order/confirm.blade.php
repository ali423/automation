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
                                        <option value="{{ $order->customer_id }}">{{ $order->customer ? $order->customer->name : 'مشتری حذف شده' }}</option>
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
                                            @if($order->orderItems->count() > 0)
                                                @foreach($order->orderItems as $item)
                                                    <option value="{{ $item->commodity_id }}" {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $item->commodity ? $item->commodity->title : 'کالا حذف شده' }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">کالایی یافت نشد</option>
                                            @endif
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب
                                            کنید.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="unit_id"> {{ __('fields.unit') }}</label>
                                        <select id="unit_id" class="form-control" name="unit_id[0]"
                                            onchange="unitchange(this)" required>
                                            @if($order->orderItems->count() > 0)
                                                @foreach($order->orderItems as $item)
                                                    <option value="{{ $item->unit_id }}" {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $item->unit_symbol }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">واحدی یافت نشد</option>
                                            @endif
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
                                        <input type="text" id="price" value="{{ $order->total_price }}" name="price[0]"
                                            class="form-control" placeholder="{{ __('fields.sell-price') }}" required>
                                        <div class="invalid-feedback">{{ __('fields.sell-price') }} را انتخاب کنید</div>
                                    </div>
                                    @php($fixed_amount = $order->orderItems->sum('commodity_amount'))
                                    @if($order->orderItems->count() > 0)
                                        @foreach($order->orderItems as $item)
                                            @if($item->commodity)
                                                <div class="input-group mb-3 wares">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"
                                                            id="انبار مرکزی">{{ $item->commodity->title }} - {{ $item->unit_symbol }}</span>
                                                    </div>
                                                    <input id="commodityamount" onkeyup="commodityamountfunc(this)" type="number" class="ware-amount form-control" min="0"
                                                        value="{{ $item->commodity_amount }}"
                                                        name="amount[{{ $item->commodity_id }}][0]"
                                                        required="">
                                                    <div class="input-group-append"><span class="input-group-text"
                                                            id="ware-amount">مقدار سفارش: {{ $item->commodity_amount }}</span>
                                                    </div>
                                                    <div class="warehouse-inputs position-relative"
                                                        style="overflow: hidden;height:0;width:0;">
                                                        <input type="text" name="warehouse_id[{{ $item->commodity_id }}][]"
                                                            value="1">
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="alert alert-warning">
                                            کالای مربوط به این سفارش حذف شده است.
                                        </div>
                                    @endif
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
