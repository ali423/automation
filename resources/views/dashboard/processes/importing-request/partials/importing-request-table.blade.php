<table id="datatable-buttons-importing-request" class="table table-striped dt-responsive nowrap w-100">
    <thead class="text-center">
    <tr>
        <th>ردیف</th>
        <th> {{ __('fields.status') }}</th>
        <th> {{ __('fields.importing_request.number') }}</th>
        <th>{{ __('fields.created_at') }}</th>
        <th>{{ __('fields.creator') }}</th>
        <th>{{ __('fields.type') }}</th>
        <th>{{ __('fields.seller') }}</th>
        <th>{{ __('fields.details') }}</th>
    </tr>
    </thead>

    <tbody class="text-center">
    @if($requests->count() > 0)
        @foreach ($requests as $request)
            <tr>
                <td>{{ $requests->firstItem() + $loop->index }}</td>
                <td>
                    @php
                        $statusClasses = [
                            'awaiting_approval' => 'badge-warning',
                            'approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            'expired' => 'badge-secondary',
                            'done' => 'badge-info'
                        ];
                        $statusClass = $statusClasses[$request->status] ?? 'badge-secondary';
                    @endphp
                    <span class="badge {{ $statusClass }}">
                        {{ __('fields.importing_request.status')[$request->status] }}
                    </span>
                </td>
                <td>{{ $request->number }}</td>
                <td>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}</td>
                <td>سیستم</td>
                <td>
                    @php($types = $request->commodities->pluck('type')->unique()->values())
                    @foreach ($types as $type)
                        <span class="badge badge-secondary">{{ __('fields.commodity.types.' . $type) }}</span>
                    @endforeach
                </td>
                <td>{{ optional($request->seller)->name ?? optional($request->seller)->comp_name ?? '-' }}</td>
                <td><a href="{{ route('importing-request.show', $request) }}" class=""><i class="ti-more-alt font-24"></i></a>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="8" class="text-center">
                <div class="alert alert-info">
                    <i class="ti-info-alt"></i>
                    @if(request('search') || request('filters'))
                        هیچ درخواست خرید با فیلترهای اعمال شده یافت نشد.
                    @else
                        هیچ درخواست خریدی یافت نشد.
                    @endif
                </div>
            </td>
        </tr>
    @endif
    </tbody>
</table>
