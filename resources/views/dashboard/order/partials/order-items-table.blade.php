@if($order->items_count > 0)
    <div class="form-row mt-4">
        <div class="col-12">
            <h5>جزئیات سفارش</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>کالا</th>
                            <th>مقدار</th>
                            <th>واحد</th>
                            <th>قیمت واحد</th>
                            <th>قیمت کل</th>
                            <th>مالیات ارزش افزوده</th>
                            <th>قیمت کل با مالیات</th>
                            @if(isset($showWeight) && $showWeight)
                                <th>وزن (کیلوگرم)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->commodity ? $item->commodity->title : 'کالا حذف شده' }}</td>
                                <td>{{ number_format($item->commodity_amount) }}</td>
                                <td>{{ $item->unit_symbol }}</td>
                                <td>{{ number_format($item->price) }} تومان</td>
                                <td>{{ number_format($item->total_price) }} تومان</td>
                                <td>{{ number_format($item->vat_amount) }} تومان</td>
                                <td>{{ number_format($item->total_price_with_vat) }} تومان</td>
                                @if(isset($showWeight) && $showWeight)
                                    <td>
                                        @if($item->weight_kg !== null)
                                            {{ number_format($item->weight_kg, 3) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-left">مجموع کل:</th>
                            <th>{{ number_format($order->total_price) }} تومان</th>
                            <th>{{ number_format($order->total_vat_amount) }} تومان</th>
                            <th>{{ number_format($order->total_price_with_vat) }} تومان</th>
                            @if(isset($showWeight) && $showWeight)
                                <th>
                                    {{ $order->total_weight_kg !== null ? number_format($order->total_weight_kg, 3) . ' کیلوگرم' : 'نامشخص' }}
                                </th>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endif
