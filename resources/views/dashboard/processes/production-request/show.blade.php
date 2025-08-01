@extends('layouts.main')
@section('title', 'جزئیات درخواست تولید')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزئیات درخواست تولید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.status') }}</label>
                                <input type="text" value="{{ $request->status_text }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.production-request.number') }}</label>
                                <input type="text" value="{{ $request->number }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.production-request.product_id') }}</label>
                                <input type="text" value="{{ $request->product ? $request->product->title : 'نامشخص' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.production-request.production_amount') }}</label>
                                <input type="text" value="{{ number_format($request->production_amount, 2) }} {{ $request->unit ? $request->unit->name : '' }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.unit') }}</label>
                                <input type="text" value="{{ $request->unit ? $request->unit->name . ' (' . $request->unit->symbol . ')' : 'نامشخص' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.created_at') }}</label>
                                <input type="text" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i', strtotime($request->created_at)) }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.creator') }}</label>
                                <input type="text" value="{{ isset($request->creator_user) ? $request->creator_user->full_name : 'سیستم' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>کل هزینه (ریال)</label>
                                <input type="text" value="{{ number_format($request->total_cost) }}" class="form-control" disabled>
                            </div>
                        </div>
                        @if($request->description)
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-12">
                                    <label>{{ __('fields.production-request.description') }}</label>
                                    <input type="text" value="{{ $request->description }}" class="form-control" disabled>
                                </div>
                            </div>
                        @endif

                        <!-- Materials Section -->
                        <div class="col-lg-12 mt-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fa fa-cubes mr-2"></i>
                                        مواد اولیه
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($request->materials->count() > 0)
                                        @if(in_array($request->status, ['approvaled', 'approved']))
                                            <!-- Show consumed materials for approved requests -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>نام ماده</th>
                                                            <th>مقدار مصرف شده</th>
                                                            <th>واحد</th>
                                                            <th>وضعیت</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($request->materials as $material)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fa fa-cube text-success mr-2"></i>
                                                                        <strong>{{ $material->title }}</strong>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="font-weight-bold text-success">
                                                                        {{ number_format($material->pivot->required_amount, 4) }}
                                                                    </span>
                                                                    <small class="text-muted d-block">
                                                                        {{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->symbol : '' }}
                                                                    </small>
                                                                </td>
                                                                <td>
                                                                    <span class="font-weight-bold">
                                                                        {{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->name : '-' }}
                                                                    </span>
                                                                    <small class="text-muted d-block">
                                                                        ({{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->symbol : '' }})
                                                                    </small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-success">
                                                                        <i class="fa fa-check-circle mr-1"></i>
                                                                        مصرف شده
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <!-- Show current inventory status for pending requests -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>نام ماده</th>
                                                            <th>مقدار مورد نیاز</th>
                                                            <th>موجودی</th>
                                                            <th>تفاوت</th>
                                                            <th>واحد</th>
                                                            <th>وضعیت</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($request->materials as $material)
                                                            @php
                                                                $inventoryService = app(\App\Services\InventoryService::class);
                                                                $unitConversionService = app(\App\Services\UnitConversionService::class);
                                                                
                                                                // First, get all available inventory for this material
                                                                $allInventory = $inventoryService->getAllInventoryForCommodity($material->id);
                                                                
                                                                // Calculate total available stock in the formula unit
                                                                $availableStock = 0;
                                                                $conversionInfo = '';
                                                                $availableUnits = [];
                                                                
                                                                foreach ($allInventory as $inventory) {
                                                                    if ($inventory->amount > 0) {
                                                                        $unit = \App\Models\Unit::find($inventory->unit_id);
                                                                        $availableUnits[] = "{$inventory->amount} {$unit->symbol}";
                                                                        
                                                                        if ($inventory->unit_id == $material->pivot->unit_id) {
                                                                            // Direct match - no conversion needed
                                                                            $availableStock += $inventory->amount;
                                                                        } else {
                                                                            // Convert from available unit to formula unit
                                                                            $convertedAmount = $unitConversionService->convert(
                                                                                $inventory->amount,
                                                                                $inventory->unit_id,
                                                                                $material->pivot->unit_id,
                                                                                $material->id
                                                                            );
                                                                            
                                                                            if ($convertedAmount !== null && $convertedAmount > 0) {
                                                                                $availableStock += $convertedAmount;
                                                                                $conversionInfo .= " (تبدیل شده از {$inventory->amount} {$unit->symbol})";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                
                                                                // If no stock found, show available inventory information
                                                                $allAvailableInfo = '';
                                                                if ($availableStock == 0 && !empty($availableUnits)) {
                                                                    $allAvailableInfo = ' (موجود در: ' . implode(', ', $availableUnits) . ')';
                                                                }
                                                                
                                                                // Determine stock status for styling
                                                                $stockStatus = 'success';
                                                                $stockIcon = 'fa-check-circle';
                                                                $statusText = 'کافی';
                                                                if ($availableStock < $material->pivot->required_amount) {
                                                                    $stockStatus = 'danger';
                                                                    $stockIcon = 'fa-exclamation-triangle';
                                                                    $statusText = 'ناکافی';
                                                                } elseif ($availableStock == $material->pivot->required_amount) {
                                                                    $stockStatus = 'warning';
                                                                    $stockIcon = 'fa-info-circle';
                                                                    $statusText = 'دقیق';
                                                                }
                                                                
                                                                // Get main unit data
                                                                $mainUnitData = $request->getMainUnitAmountAttribute();
                                                                $materialMainUnit = $mainUnitData[$material->id] ?? null;
                                                            @endphp
                                                        
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fa fa-cube text-primary mr-2"></i>
                                                                    <strong>{{ $material->title }}</strong>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="font-weight-bold text-primary">
                                                                    {{ number_format($material->pivot->required_amount, 4) }}
                                                                </span>
                                                                <small class="text-muted d-block">
                                                                    {{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->symbol : '' }}
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <span class="font-weight-bold text-{{ $stockStatus }}">
                                                                    {{ number_format($availableStock, 4) }}
                                                                </span>
                                                                <small class="text-muted d-block">
                                                                    {{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->symbol : '' }}
                                                                </small>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $difference = $availableStock - $material->pivot->required_amount;
                                                                    $differenceColor = 'success';
                                                                    $differenceIcon = 'fa-plus';
                                                                    $differenceText = 'مازاد';
                                                                    
                                                                    if ($difference < 0) {
                                                                        $differenceColor = 'danger';
                                                                        $differenceIcon = 'fa-minus';
                                                                        $differenceText = 'کمبود';
                                                                        $difference = abs($difference);
                                                                    } elseif ($difference == 0) {
                                                                        $differenceColor = 'warning';
                                                                        $differenceIcon = 'fa-equals';
                                                                        $differenceText = 'دقیق';
                                                                    }
                                                                @endphp
                                                                <span class="font-weight-bold text-{{ $differenceColor }}">
                                                                    {{ $differenceIcon == 'fa-minus' ? '-' : ($differenceIcon == 'fa-plus' ? '+' : '') }}{{ number_format($difference, 4) }}
                                                                </span>
                                                                <small class="text-muted d-block">
                                                                    {{ $differenceText }}
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <span class="font-weight-bold">
                                                                    {{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->name : '-' }}
                                                                </span>
                                                                <small class="text-muted d-block">
                                                                    ({{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->symbol : '' }})
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-{{ $stockStatus }}">
                                                                    <i class="fa {{ $stockIcon }} mr-1"></i>
                                                                    {{ $statusText }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                            <h6 class="text-muted">هیچ ماده اولیه‌ای برای این درخواست تعریف نشده است.</h6>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
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

                        <!-- Comments Section -->
                        @if($request->comments->count() > 0)
                            <div class="col-lg-12 mt-4">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fa fa-comments mr-2"></i>
                                            نظرات و توضیحات
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($request->comments as $comment)
                                            <div class="card mb-3 border-left-success">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fa fa-user-circle fa-2x text-success mr-2"></i>
                                                                <div>
                                                                    <strong>{{ $comment->user->full_name }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i', strtotime($comment->created_at)) }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <p class="mb-0">{{ $comment->body }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Files Section -->
                        @if($request->files->count() > 0)
                            <div class="col-lg-12 mt-4">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fa fa-paperclip mr-2"></i>
                                            فایل‌های ضمیمه
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($request->files as $file)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="card h-100 border-primary">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fa fa-file fa-2x text-primary mr-3"></i>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1">{{ $file->name }}</h6>
                                                                    <small class="text-muted">
                                                                        {{ $file->user->full_name }} - 
                                                                        {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($file->created_at)) }}
                                                                    </small>
                                                                </div>
                                                                <a href="{{ asset(str_replace('public', 'storage', $file->source)) }}" 
                                                                   download="{{ $file->name }}"
                                                                   class="btn btn-sm btn-outline-primary">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="row mt-3">
                            <div class="col-md-6 mb-1 mb-md-0">
                                @if($request->is_editable)
                                    <a href="{{ route('production-request.edit', $request) }}" class="btn btn-primary">ویرایش</a>
                                @endif
                                @if($request->is_deletable)
                                    <form method="post" action="{{ route('production-request.destroy', $request) }}" class="d-inline w-50">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('آیا از حذف این درخواست مطمئن هستید؟');">حذف</button>
                                    </form>
                                @endif
                            </div>
                            <div class="col-md-6 text-md-right">
                                @if($request->can_be_approved)
                                    <a href="{{ route('approval.production', $request) }}" class="btn btn-success px-2">تایید درخواست</a>
                                @endif
                                @if($request->can_be_rejected)
                                    <a href="{{ route('reject.production', $request) }}" class="btn btn-warning px-2">رد درخواست</a>
                                @endif
                                <a href="{{ route('activity.index', [
                                    'object_id' => $request->id,
                                    'object_type' => class_basename($request),
                                ]) }}"
                                    class="btn btn-dfprimary px-1 px-md-4 m-md-0">تاریخچه تغییرات</a>
                                <a href="{{ route('production-request.index') }}" class="btn btn-secondary px-2 px-md-4 m-md-0">بازگشت</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection 