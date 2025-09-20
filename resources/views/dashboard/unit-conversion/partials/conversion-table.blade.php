{{-- Unit conversion table partial for index view --}}
<div class="mb-2">
    <small class="text-muted">
        <i class="ti-info-circle"></i> 
        مرتب‌سازی فقط برای رکوردهای صفحه فعلی اعمال می‌شود
    </small>
</div>
<table id="datatable-buttons-conversion" class="table table-striped dt-responsive nowrap w-100">
    <thead class="text-center">
    <tr>
        <th>ردیف</th>
        <th>{{ __('fields.commodity.name') }}</th>
        <th>{{ __('fields.from_unit') }}</th>
        <th>{{ __('fields.to_unit') }}</th>
        <th>{{ __('fields.conversion_rate') }}</th>
        <th>{{ __('fields.created_at') }}</th>
        <th>{{ __('fields.details') }}</th>
    </tr>
    </thead>

    <tbody class="text-center">
    @forelse ($conversions as $index => $conversion)
        <tr>
            <td>{{ $conversions->firstItem() + $index }}</td>
            <td>{{ $conversion->commodity->title ?? '-' }}</td>
            <td>{{ $conversion->fromUnit->name ?? '-' }} ({{ $conversion->fromUnit->symbol ?? '-' }})</td>
            <td>{{ $conversion->toUnit->name ?? '-' }} ({{ $conversion->toUnit->symbol ?? '-' }})</td>
            <td>{{ number_format($conversion->conversion_rate, 2) }}</td>
            <td>{{ jdate($conversion->created_at)->format('Y/m/d') }}</td>
            <td>
                <a href="{{ route('unit-conversion.show', $conversion) }}" class=""><i class="ti-more-alt font-24"></i></a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center text-muted py-4">
                <i class="ti-info-circle font-24 mb-2"></i><br>
                {{ __('pagination.no_results') }}
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
