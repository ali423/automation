@extends('layouts.main')
@section('title', 'تحویل سفارش')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">تحویل سفارش</h4>
                
                <!-- Order Summary -->
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>{{ __('fields.customer') }}</label>
                        <input type="text" class="form-control" value="{{ $order->customer ? $order->customer->name : 'مشتری حذف شده' }}" disabled>
                    </div>
                    <div class="form-group col-md-3">
                        <label>{{ __('fields.deadline') }}</label>
                        <input type="text" class="form-control" value="{{ $order->deadline }}" disabled>
                    </div>
                    <div class="form-group col-md-3">
                        <label>{{ __('fields.status') }}</label>
                                                    <input type="text" class="form-control" value="{{ __('fields.order.status.' . $order->status) }}" disabled>
                    </div>
                    <div class="form-group col-md-3">
                        <label>تعداد کالاها</label>
                        <input type="text" class="form-control" value="{{ $order->items_count }} کالا" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('order-confirm.store',$order) }}"
                            class="needs-validation" novalidate="">
                            @csrf
                            <div class="form-row m-3">
                                <div class="form-group col">
                                    <label for="customer_id">{{ __('fields.customer') }}</label>
                                    <select id="customer_id" class="form-control" name="customer_id" required>
                                        <option value="{{ $order->customer_id }}">{{ $order->customer ? $order->customer->name : 'مشتری حذف شده' }}</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ __('fields.customer') }} را انتخاب کنید
                                    </div>
                                </div>
                            </div>
                            
                            <div id="product_formul" class="col-lg-12">
                                <p>اطلاعات کالا</p>
                                @if($order->items_count > 0)
                                    @foreach($order->orderItems as $item)
                                        @if($item->commodity)
                                            <div id="inputFormRow_{{ $loop->index }}" class="form-row shadow p-4 mb-3">
                                                <div class="form-group col-md-4">
                                                    <label for="commodity_id_{{ $loop->index }}">{{ __('fields.commodity.name') }}</label>
                                                    <select id="commodity_id_{{ $loop->index }}" class="form-control" name="commodity_id[{{ $loop->index }}]" required>
                                                        <option value="{{ $item->commodity_id }}" selected>
                                                            {{ $item->commodity ? $item->commodity->title : 'کالا حذف شده' }}
                                                        </option>
                                                    </select>
                                                    <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید</div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="unit_id_{{ $loop->index }}">{{ __('fields.unit') }}</label>
                                                    <select id="unit_id_{{ $loop->index }}" class="form-control" name="unit_id[{{ $loop->index }}]" required>
                                                        <option value="{{ $item->unit_id }}" selected>
                                                            {{ $item->unit ? $item->unit->symbol : 'نامشخص' }}
                                                        </option>
                                                    </select>
                                                    <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="amount_{{ $loop->index }}">{{ __('fields.commodity.amount') }}</label>
                                                    <input id="commodityamount_{{ $loop->index }}" onkeyup="commodityamountfunc(this)" type="number" class="ware-amount form-control" min="0" step="0.01"
                                                        value="{{ $item->commodity_amount }}"
                                                        name="amount[{{ $loop->index }}]"
                                                        required="">
                                                    <div class="invalid-feedback">{{ __('fields.commodity.amount') }} الزامی است</div>
                                                    <small class="form-text text-muted">مقدار سفارش: {{ $item->commodity_amount }}</small>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="price_{{ $loop->index }}">{{ __('fields.sell-price') }}</label>
                                                    <input type="number" id="price_{{ $loop->index }}" value="{{ $item->price }}" name="price[{{ $loop->index }}]"
                                                        class="form-control" placeholder="{{ __('fields.sell-price') }}" step="0.01" required>
                                                    <div class="invalid-feedback">{{ __('fields.sell-price') }} را انتخاب کنید</div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>واحد</label>
                                                    <input type="text" class="form-control" value="{{ $item->unit ? $item->unit->name : 'نامشخص' }}" readonly>
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

                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default"
                                    accept="image/*,.pdf,.zip,.rar">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled=""
                                        placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">انتخاب فایل</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-20">
                                <label for="comment">توضیحات</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment" rows="6" placeholder="توضیحات اضافی در مورد تحویل...">{{ old('comment') }}</textarea>
                            </div>

                            

                            <button type="submit" class="btn btn-primary mr-2">ثبت درخواست</button>
                            <a href="{{ route('order.show', $order) }}" class="btn btn-secondary mr-2">مشاهده سفارش</a>
                            <a href="{{ route('order.index') }}" class="btn btn-danger">انصراف</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')

    <script src="{{ asset('js/default-assets/active.js') }}"></script>
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/default-assets/file-upload.js') }}"></script>
    
    <script type="text/javascript">
        function commodityamountfunc(e){
            var value = parseFloat(e.value);
            if (isNaN(value) || value < 0) {
                e.value = 0;
            }
            
            var originalAmount = parseFloat(e.getAttribute('data-original-amount') || 0);
            if (value > originalAmount) {
                alert('مقدار تحویل نمی‌تواند بیشتر از مقدار سفارش باشد.');
                e.value = originalAmount;
            }
        }
        
        $(document).ready(function() {
            $('.ware-amount').each(function() {
                var originalAmount = $(this).val();
                $(this).attr('data-original-amount', originalAmount);
            });
        });
    </script>

@endsection
