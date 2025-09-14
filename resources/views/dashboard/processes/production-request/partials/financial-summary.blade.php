{{-- Financial Summary Partial for Production Requests --}}
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fa fa-chart-pie mr-2"></i>
                    خلاصه مالی
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @php
                        $totalCost = $request->total_cost;
                        $productionAmount = $request->production_amount;
                        $unitCost = $productionAmount > 0 ? $totalCost / $productionAmount : 0;
                    @endphp
                    <div class="col-4">
                        <h4 class="text-primary">{{ number_format($totalCost) }}</h4>
                        <small class="text-muted">کل هزینه (ریال)</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success">{{ number_format($unitCost, 0) }}</h4>
                        <small class="text-muted">هزینه واحد (ریال)</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-info">{{ number_format($productionAmount, 2) }}</h4>
                        <small class="text-muted">مقدار تولید</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">
                    <i class="fa fa-chart-bar mr-2"></i>
                    آمار مواد
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-warning">{{ $request->materials->count() }}</h4>
                        <small class="text-muted">کل مواد</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-{{ in_array($request->status, ['approvaled', 'approved']) ? 'success' : 'info' }}">
                            @if(in_array($request->status, ['approvaled', 'approved']))
                                مصرف شده
                            @else
                                در انتظار تایید
                            @endif
                        </h4>
                        <small class="text-muted">وضعیت</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
