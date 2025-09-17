<table id="datatable-buttons-inventory" class="table table-striped dt-responsive nowrap w-100">
    <thead class="text-center">
    <tr>
        <th>ردیف</th>
        <th>کالا</th>
        <th>واحد</th>
        <th>مقدار موجودی</th>
        <th>قیمت خرید</th>
        <th>قیمت فروش</th>
        <th>{{ __('fields.details') }}</th>
    </tr>
    </thead>

    <tbody class="text-center">
    @if($inventories->count() > 0)
        @php($i = 1)
        @foreach ($inventories as $inventory)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $inventory->commodity->title ?? 'نامشخص' }}</td>
                <td>{{ $inventory->unit->name ?? 'نامشخص' }}</td>
                <td>{{ number_format($inventory->amount, 2) }}</td>
                <td>{{ number_format($inventory->purchase_price ?? 0) }} تومان</td>
                <td>
                    @if(isset($inventory->financial_data) && $inventory->financial_data['is_product'])
                        {{ number_format($inventory->financial_data['sale_price']) }} تومان
                        <small class="d-block text-muted">محاسبه شده</small>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td><a href="{{ route('inventory.show', $inventory) }}" class=""><i
                            class="ti-more-alt font-24"></i></a>
                </td>
            </tr>
            @php($i++)
        @endforeach
    @else
        <tr>
            <td colspan="7" class="text-center">
                <div class="alert alert-info">
                    <i class="ti-info-alt"></i>
                    {{ $message ?? 'هیچ موجودی یافت نشد.' }}
                </div>
            </td>
        </tr>
    @endif
    </tbody>
</table>
