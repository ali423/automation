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
                             <p>مواد اولیه مورد نیاز 
                                 @if(in_array($request->status, ['approvaled', 'approved']))
                                     (مصرف شده)
                                 @else
                                     (وضعیت موجودی)
                                 @endif
                             </p>
                            
                            @if($request->materials->count() > 0)
                                @foreach ($request->materials as $material)
                                    <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                        <div class="showbarrel">
                                            <i class="fa fa-database"></i>
                                            <div>
                                                <span>Main Unit Amount</span>
                                                @php
                                                    $mainUnitData = $request->getMainUnitAmountAttribute();
                                                    $materialMainUnit = $mainUnitData[$material->id] ?? null;
                                                @endphp
                                                <span>
                                                    @if($materialMainUnit && isset($materialMainUnit['main_unit_amount']))
                                                        {{ number_format($materialMainUnit['main_unit_amount'], 4) }} {{ $materialMainUnit['main_unit_name'] . ' (' . $materialMainUnit['main_unit_symbol'] . ')' }}
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>{{ __('fields.commodity.name') }}</label>
                                            <input type="text" value="{{ $material->title }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>مقدار مورد نیاز</label>
                                            <input type="text" value="{{ number_format($material->pivot->required_amount, 4) }} {{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->symbol : '' }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>نسبت فرمول</label>
                                            <input type="text" value="{{ number_format($material->pivot->amount, 4) }} {{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->symbol : '' }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>واحد</label>
                                            <input type="text" value="{{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->name . ' (' . \App\Models\Unit::find($material->pivot->unit_id)->symbol . ')' : '-' }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>موجودی موجود</label>
                                            @php
                                                $inventoryService = app(\App\Services\InventoryService::class);
                                                $unitConversionService = app(\App\Services\UnitConversionService::class);
                                                
                                                // Get stock in the formula unit
                                                $availableStock = $inventoryService->getStockLevel($material->id, $material->pivot->unit_id);
                                                
                                                // If no stock in formula unit, try to find stock in other units and convert
                                                $convertedStock = 0;
                                                $conversionInfo = '';
                                                
                                                if ($availableStock == 0) {
                                                    $allInventory = $inventoryService->getAllInventoryForCommodity($material->id);
                                                    
                                                    foreach ($allInventory as $inventory) {
                                                        if ($inventory->amount > 0) {
                                                            // Try to convert from available unit to formula unit
                                                            $convertedAmount = $unitConversionService->convert(
                                                                $inventory->amount,
                                                                $inventory->unit_id,
                                                                $material->pivot->unit_id,
                                                                $material->id
                                                            );
                                                            
                                                            if ($convertedAmount !== null && $convertedAmount > 0) {
                                                                $convertedStock += $convertedAmount;
                                                                $availableUnit = \App\Models\Unit::find($inventory->unit_id);
                                                                $conversionInfo .= " (تبدیل شده از {$inventory->amount} {$availableUnit->symbol})";
                                                            }
                                                        }
                                                    }
                                                    
                                                    // If we have converted stock, use it
                                                    if ($convertedStock > 0) {
                                                        $availableStock = $convertedStock;
                                                    }
                                                }
                                                
                                                // If still no stock, show all available inventory for information
                                                $allAvailableInfo = '';
                                                if ($availableStock == 0) {
                                                    $allInventory = $inventoryService->getAllInventoryForCommodity($material->id);
                                                    $availableUnits = [];
                                                    
                                                    foreach ($allInventory as $inventory) {
                                                        if ($inventory->amount > 0) {
                                                            $unit = \App\Models\Unit::find($inventory->unit_id);
                                                            $availableUnits[] = "{$inventory->amount} {$unit->symbol}";
                                                        }
                                                    }
                                                    
                                                    if (!empty($availableUnits)) {
                                                        $allAvailableInfo = ' (موجود در: ' . implode(', ', $availableUnits) . ')';
                                                    }
                                                }
                                            @endphp
                                            <input type="text" value="{{ number_format($availableStock, 4) }} {{ \App\Models\Unit::find($material->pivot->unit_id) ? \App\Models\Unit::find($material->pivot->unit_id)->symbol : '' }}{{ $conversionInfo }}{{ $allAvailableInfo }}" class="form-control" disabled>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <p class="mb-0">هیچ ماده اولیه‌ای برای این درخواست تعریف نشده است.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Financial Summary -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">خلاصه مالی</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <h6>محاسبات:</h6>
                                            <ul class="mb-0">
                                                @php
                                                    $totalCost = $request->total_cost;
                                                    $productionAmount = $request->production_amount;
                                                    $unitCost = $productionAmount > 0 ? $totalCost / $productionAmount : 0;
                                                @endphp
                                                <li>کل هزینه مواد اولیه: {{ number_format($totalCost) }} ریال</li>
                                                <li>هزینه واحد تولید: {{ number_format($unitCost, 0) }} ریال</li>
                                                <li>مقدار تولید: {{ number_format($productionAmount, 2) }} {{ $request->unit ? $request->unit->symbol : '' }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">آمار مواد</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <h6>آمار مواد:</h6>
                                            <ul class="mb-0">
                                                <li>کل مواد: {{ $request->materials->count() }} مورد</li>
                                                <li>وضعیت: 
                                                    @if(in_array($request->status, ['approvaled', 'approved']))
                                                        <span class="text-success">مصرف شده</span>
                                                    @else
                                                        <span class="text-info">در انتظار تایید</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Comments Section -->
                        @if($request->comments->count() > 0)
                            <div class="col-lg-12 mt-4">
                                <p>نظرات و توضیحات</p>
                                @foreach ($request->comments as $comment)
                                    <div class="form-row shadow p-4 mb-3">
                                        <div class="form-group col-md-3">
                                            <label>کاربر</label>
                                            <input type="text" value="{{ $comment->user->full_name }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>تاریخ</label>
                                            <input type="text" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i', strtotime($comment->created_at)) }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>نظر</label>
                                            <input type="text" value="{{ $comment->body }}" class="form-control" disabled>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Files Section -->
                        @if($request->files->count() > 0)
                            <div class="col-lg-12 mt-4">
                                <p>فایل‌های ضمیمه</p>
                                @foreach ($request->files as $file)
                                    <div class="form-row shadow p-4 mb-3">
                                        <div class="form-group col-md-4">
                                            <label>نام فایل</label>
                                            <input type="text" value="{{ $file->name }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>کاربر</label>
                                            <input type="text" value="{{ $file->user->full_name }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>تاریخ</label>
                                            <input type="text" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i', strtotime($file->created_at)) }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>عملیات</label>
                                            <a href="{{ asset(str_replace('public', 'storage', $file->source)) }}" 
                                               download="{{ $file->name }}"
                                               class="btn btn-primary btn-block">
                                                <i class="ti-download"></i> دانلود
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
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