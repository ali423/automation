@if($order->items_count > 0)
    @php
        $hasDiscount = $order->orderItems->contains(function($item) {
            return $item->discount_percentage !== null && $item->discount_percentage > 0;
        });
    @endphp
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
                            @if($hasDiscount)
                                <th>تخفیف</th>
                            @endif
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
                                <td>
                                    @if($item->commodity && $item->inventory)
                                        <a href="{{ route('inventory.show', $item->inventory) }}">{{ $item->commodity->title }}</a>
                                    @elseif($item->commodity)
                                        {{ $item->commodity->title }}
                                    @else
                                        کالا حذف شده
                                    @endif
                                </td>
                                <td>{{ number_format($item->commodity_amount) }}</td>
                                <td>{{ $item->unit_symbol }}</td>
                                <td>{{ number_format($item->price) }} ریال</td>
                                @if($hasDiscount)
                                    <td>
                                        @if($item->discount_percentage)
                                            {{ $item->discount_percentage }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                                <td>{{ number_format($item->total_price) }} ریال</td>
                                <td>{{ number_format($item->vat_amount) }} ریال</td>
                                <td>{{ number_format($item->total_price_with_vat) }} ریال</td>
                                @if(isset($showWeight) && $showWeight)
                                    <td>
                                        @if($item->weight_kg !== null)
                                            {{ number_format($item->weight_kg, 0) }}
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
                            <th colspan="{{ $hasDiscount ? '6' : '5' }}" class="text-left">مجموع کل:</th>
                            <th>{{ number_format($order->total_price) }} ریال</th>
                            <th>{{ number_format($order->total_vat_amount) }} ریال</th>
                            <th>{{ number_format($order->total_price_with_vat) }} ریال</th>
                            @if(isset($showWeight) && $showWeight)
                                <th>
                                    {{ $order->total_weight_kg !== null ? number_format($order->total_weight_kg, 0) . ' کیلوگرم' : 'نامشخص' }}
                                </th>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endif
