<table id="datatable-buttons-inventory" class="table table-striped dt-responsive nowrap w-100">
    <thead class="text-center">
    <tr>
        <th>ردیف</th>
        <th>کالا</th>
        <th>شناسه کالا</th>
        <th>واحد</th>
        <th>مقدار موجودی</th>
        <th>{{ __('fields.details') }}</th>
    </tr>
    </thead>

    <tbody class="text-center">
    @if($inventories->count() > 0)
        @foreach ($inventories as $inventory)
            <tr>
                <td>{{ $inventories->firstItem() + $loop->index }}</td>
                <td>{{ $inventory->commodity->title ?? 'نامشخص' }}</td>
                <td>{{ $inventory->commodity->product_identifier ?? '-' }}</td>
                <td>{{ $inventory->unit->name ?? 'نامشخص' }}</td>
                <td>{{ number_format($inventory->amount, 2) }}</td>
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
