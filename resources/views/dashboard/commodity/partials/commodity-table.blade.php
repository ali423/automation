{{-- Commodity table partial for index view --}}
<table id="datatable-buttons-commodity" class="table table-striped dt-responsive nowrap w-100">
    <thead class="text-center">
    <tr>
        <th>ردیف</th>
        <th> {{ __('fields.title') }}</th>
        <th> {{ __('fields.commodity.number') }}</th>
        <th>شناسه کالا</th>
        <th> {{ __('fields.base_price') }}</th>
        <th> {{ __('fields.type') }}</th>
        <th>{{ __('fields.unit') }}</th>
        <th>{{ __('fields.details') }}</th>
    </tr>
    </thead>

    <tbody class="text-center">
    @foreach ($commodities as $index => $commodity)
        <tr>
            <td>{{ $commodities->firstItem() + $index }}</td>
            <td>{{ $commodity->title }}</td>
            <td>{{ $commodity->number }}</td>
            <td>{{ $commodity->type == 'product' ? ($commodity->product_identifier ?? '-') : '-' }}</td>
            <td>{{ number_format($commodity->base_price ?? 0) }}</td>
            <td>{{ __('fields.commodity.types')[$commodity->type] }}</td>
            <td>{{ $commodity->unit ? $commodity->unit->name . ' (' . $commodity->unit->symbol . ')' : '-' }}</td>
            <td><a href="{{ route('commodity.show', $commodity) }}" class=""><i
                        class="ti-more-alt font-24"></i></a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
