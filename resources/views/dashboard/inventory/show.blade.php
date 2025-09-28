@extends('layouts.main')
@section('title', 'نمایش موجودی')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات موجودی</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        {{-- Basic Information Display --}}
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label>کالا</label>
                                <input type="text" value="{{ $inventory->commodity->title ?? 'نامشخص' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>واحد</label>
                                <input type="text" value="{{ $inventory->unit->name ?? 'نامشخص' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>مقدار موجودی</label>
                                <input type="text" value="{{ number_format($inventory->amount, 2) }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>قیمت خرید (ریال)</label>
                                <input type="text" value="{{ number_format($inventory->purchase_price ?? 0) }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label>قیمت فروش (ریال)</label>
                                <input type="text" value="{{ isset($financialData) && $financialData['has_sale_price'] ? number_format($financialData['sale_price']) : 'محاسبه نشده' }}" class="form-control" disabled>
                                <small class="form-text text-muted">
                                    @if(isset($financialData) && $financialData['is_product'])
                                        قیمت بر اساس درصد سود کالا محاسبه می‌شود
                                    @else
                                        <span class="text-warning"><i class="ti-info-circle"></i> مواد اولیه قیمت فروش ندارند</span>
                                    @endif
                                </small>
                            </div>
                            <div class="form-group col-md-6">
                                <label>ارزش کل موجودی (ریال)</label>
                                <input type="text" value="{{ isset($financialData) ? number_format($financialData['total_value']) : 'محاسبه نشده' }}" class="form-control" disabled>
                                <small class="form-text text-muted">
                                    @if(isset($financialData) && $financialData['is_product'])
                                        بر اساس قیمت فروش محاسبه شده
                                    @else
                                        بر اساس قیمت خرید محاسبه شده
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label>تاریخ ایجاد</label>
                                <input type="text" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i', strtotime($inventory->created_at)) }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label>آخرین بروزرسانی</label>
                                <input type="text" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i', strtotime($inventory->updated_at)) }}" class="form-control" disabled>
                            </div>
                        </div>

                        {{-- Financial Information and Quick Actions --}}
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">اطلاعات مالی</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <h6>محاسبات:</h6>
                                            <ul class="mb-0">
                                                @if(isset($financialData) && $financialData['is_product'])
                                                    <li>سود: {{ number_format($financialData['profit']) }} ریال</li>
                                                    <li>درصد سود: {{ number_format($financialData['profit_percentage'], 1) }}%</li>
                                                    <li>ارزش کل موجودی: {{ number_format($financialData['total_value']) }} ریال</li>
                                                @else
                                                    <li>قیمت خرید: {{ number_format($financialData['purchase_price'] ?? 0) }} ریال</li>
                                                    <li>ارزش کل موجودی: {{ number_format($financialData['total_value'] ?? 0) }} ریال</li>
                                                    <li class="text-warning"><i class="ti-info-circle"></i> مواد اولیه قیمت فروش ندارند</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">عملیات سریع</h5>
                                    </div>
                                    <div class="card-body">
                                        @can('adjustStock', $inventory)
                                            <button type="button" class="btn btn-primary btn-block mb-2" data-toggle="modal" data-target="#stockAdjustmentModal">
                                                <i class="fa fa-plus-minus"></i> تنظیم موجودی
                                            </button>
                                        @endcan

                                        @if(isset($financialData) && $financialData['is_product'])
                                            <a href="{{ route('commodity.edit', $inventory->commodity) }}" class="btn btn-info btn-block">
                                                <i class="ti-settings"></i> تنظیم درصد سود
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-primary">ویرایش</a>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('activity.index', [
                                    'object_id' => $inventory->id,
                                    'object_type' => class_basename($inventory),
                                ]) }}"
                                   class="btn btn-dfprimary px-2 px-md-4 m-md-0">تاریخچه تغییرات</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock Adjustment Modal --}}
    @can('adjustStock', $inventory)
    <div class="modal fade" id="stockAdjustmentModal" tabindex="-1" role="dialog" aria-labelledby="stockAdjustmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stockAdjustmentModalLabel">تنظیم موجودی</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inventory.adjust-stock', $inventory) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="adjustment_type">نوع تنظیم <span class="text-danger">*</span></label>
                            <select name="adjustment_type" id="adjustment_type" class="form-control" required>
                                <option value="">انتخاب کنید</option>
                                <option value="add">افزودن</option>
                                <option value="subtract">کاهش</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">مقدار <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="quantity" id="quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="reason">دلیل</label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="دلیل تغییر (اختیاری)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-primary">تایید</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan


@endsection

@section('page_scripts')
    @include('dashboard.inventory.partials.form-scripts')
@endsection
