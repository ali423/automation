<table id="datatable-buttons-inventory" class="table table-striped dt-responsive nowrap w-100">
    <thead class="text-center">
    <tr>
        <th>ردیف</th>
        <th>کالا</th>
        <th>واحد</th>
        <th>مقدار موجودی</th>
        <th>تعداد بسته بندی</th>
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
            @endphp
            <tr>
                <td>{{ $inventories->firstItem() + $loop->index }}</td>
                <td>{{ $inventory->commodity->title ?? 'نامشخص' }}</td>
                <td>{{ $inventory->unit->name ?? 'نامشخص' }}</td>
                <td>{{ number_format($inventory->amount, 2) }}</td>
                <td>{{ $packagingQuantity !== '-' ? number_format($packagingQuantity, 0, '.', ',') : '-' }}</td>
                <td><a href="{{ route('inventory.show', $inventory) }}" class=""><i
                            class="ti-more-alt font-24"></i></a>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="6" class="text-center">
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
