{{-- Commodity table partial for index view --}}
<div class="mb-2">
    <small class="text-muted">
        <i class="ti-info-circle"></i> 
        مرتب‌سازی فقط برای رکوردهای صفحه فعلی اعمال می‌شود
    </small>
</div>
<table id="datatable-buttons-commodity" class="table table-striped dt-responsive nowrap w-100">
    <thead class="text-center">
    <tr>
        <th>ردیف</th>
        <th>{{ __('fields.title') }}</th>
        <th>{{ __('fields.commodity.number') }}</th>
        <th>شناسه کالا</th>
        <th>{{ __('fields.base_price') }}</th>
        <th>قیمت فروش با احتساب سود</th>
        <th>{{ __('fields.type') }}</th>
        <th>{{ __('fields.unit') }}</th>
        <th>{{ __('fields.details') }}</th>
    </tr>
    </thead>

    <tbody class="text-center">
    @forelse ($commodities as $index => $commodity)
        <tr>
            <td>{{ $commodities->firstItem() + $index }}</td>
            <td>{{ $commodity->title }}</td>
            <td>{{ $commodity->number }}</td>
            <td>{{ $commodity->type == 'product' ? ($commodity->product_identifier ?? '-') : '-' }}</td>
            <td>{{ number_format($commodity->base_price ?? 0) }}</td>
            <td>{{ $commodity->sales_price !== null ? number_format($commodity->sales_price) : '-' }}</td>
            <td>{{ __('fields.commodity.types')[$commodity->type] }}</td>
            <td>{{ $commodity->unit ? $commodity->unit->name . ' (' . $commodity->unit->symbol . ')' : '-' }}</td>
            <td><a href="{{ route('commodity.show', $commodity) }}" class=""><i
                        class="ti-more-alt font-24"></i></a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center text-muted py-4">
                <i class="ti-info-circle font-24 mb-2"></i><br>
                {{ __('pagination.no_results') }}
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
