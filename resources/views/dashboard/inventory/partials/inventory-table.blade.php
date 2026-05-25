<table id="datatable-buttons-inventory" class="table table-striped dt-responsive nowrap w-100">
    <thead class="text-center">
    <tr>
        <th>ردیف</th>
        <th>کالا</th>
        <th>واحد</th>
        <th>مقدار موجودی</th>
        <th class="d-none">قیمت (ریال)</th>
        <th>تعداد بسته بندی</th>
        <th>{{ __('fields.warning_difference') }}</th>
        <th>{{ __('fields.details') }}</th>
    </tr>
    </thead>

    <tbody class="text-center">
    @php
        $commodityUnitService = app(\App\Services\CommodityUnitService::class);
    @endphp
    @if($inventories->count() > 0)
        @foreach ($inventories as $inventory)
            @php
                // Calculate packaging quantity (cartons) using the same logic as withdrawal requests
                $packagingQuantity = '-';
                $piecesPerBox = $inventory->commodity->pieces_per_box ?? 1;
                if ($piecesPerBox > 0) {
                    // Convert inventory amount to main unit, then divide by pieces per box
                    $amountInMainUnit = $commodityUnitService->convertToMainUnit($inventory->commodity, $inventory->amount, $inventory->unit_id);
                    $packagingQuantity = $amountInMainUnit !== null ? floor($amountInMainUnit / $piecesPerBox) : '-';
                }
                // Determine unit price: products use sales price, others fall back to purchase price
                $unitPrice = null;
                if (($inventory->commodity->type ?? null) === 'product' && !is_null($inventory->commodity->sales_price)) {
                    $unitPrice = $inventory->commodity->sales_price;
                } elseif (!is_null($inventory->purchase_price)) {
                    $unitPrice = $inventory->purchase_price;
                }
            @endphp
            <tr>
                <td>{{ $inventories->firstItem() + $loop->index }}</td>
                <td>{{ $inventory->commodity->title ?? 'نامشخص' }}</td>
                <td>{{ $inventory->unit->name ?? 'نامشخص' }}</td>
                <td>{{ number_format($inventory->amount, 2) }}</td>
                <td class="d-none">{{ !is_null($unitPrice) ? number_format($unitPrice, 0, '.', ',') : '-' }}</td>
                <td>{{ $packagingQuantity !== '-' ? number_format($packagingQuantity, 0, '.', ',') : '-' }}</td>
                <td>
                    @if(!is_null($inventory->warning_difference ?? null))
                        @php
                            $difference = $inventory->warning_difference;
                            if ($difference < 0) {
                                $differenceColor = 'danger';
                                $differenceText = 'کمبود';
                            } elseif ($difference > 0) {
                                $differenceColor = 'success';
                                $differenceText = 'مازاد';
                            } else {
                                $differenceColor = 'warning';
                                $differenceText = 'در حد هشدار';
                            }
                        @endphp
                        <span class="font-weight-bold text-{{ $differenceColor }}">
                            {{ $difference < 0 ? '-' : ($difference > 0 ? '+' : '') }}{{ number_format(abs($difference), 2) }}
                        </span>
                        <small class="text-muted d-block">{{ $differenceText }}</small>
                        <small class="text-muted d-block">{{ $inventory->commodity->unit->symbol ?? '' }}</small>
                    @else
                        -
                    @endif
                </td>
                <td><a href="{{ route('inventory.show', $inventory) }}" class=""><i
                            class="ti-more-alt font-24"></i></a>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="8" class="text-center">
                <div class="alert alert-info">
                    <i class="ti-info-alt"></i>
                    @if(request('search') || request('filters'))
                        هیچ موجودی با فیلترهای اعمال شده یافت نشد.
                    @else
                        هیچ موجودی فعالی یافت نشد. موجودی ها از طریق فرآیندهای خرید و فروش ایجاد می‌شوند.
                    @endif
                </div>
            </td>
        </tr>
    @endif
    </tbody>
</table>
