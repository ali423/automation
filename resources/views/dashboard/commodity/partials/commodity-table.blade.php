{{-- Commodity table partial for index view --}}
<table id="datatable-buttons-commodity" class="table table-striped dt-responsive nowrap w-100">
    <thead class="text-center">
    <tr>
        <th>ردیف</th>
        <th>
            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
               class="text-decoration-none text-dark">
                {{ __('fields.title') }}
                @if(request('sort_by') === 'title')
                    <i class="ti-arrow-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                @endif
            </a>
        </th>
        <th>
            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'number', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
               class="text-decoration-none text-dark">
                {{ __('fields.commodity.number') }}
                @if(request('sort_by') === 'number')
                    <i class="ti-arrow-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                @endif
            </a>
        </th>
        <th>شناسه کالا</th>
        <th>
            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'purchase_price', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
               class="text-decoration-none text-dark">
                {{ __('fields.base_price') }}
                @if(request('sort_by') === 'purchase_price')
                    <i class="ti-arrow-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                @endif
            </a>
        </th>
        <th>
            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'type', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
               class="text-decoration-none text-dark">
                {{ __('fields.type') }}
                @if(request('sort_by') === 'type')
                    <i class="ti-arrow-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                @endif
            </a>
        </th>
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
