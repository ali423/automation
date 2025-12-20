@extends('layouts.main')
@section('title', 'ویرایش موجودی')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش موجودی</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('inventory.update', $inventory) }}" class="needs-validation"
                              novalidate="">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="commodity_id">کالا</label>
                                    <select id="commodity_id" class="form-control @error('commodity_id') is-invalid @enderror" name="commodity_id" required>
                                        <option value="">انتخاب کنید...</option>
                                        @foreach($formData['commodities'] as $commodity)
                                            <option value="{{ $commodity->id }}" {{ (old('commodity_id', $inventory->commodity_id) == $commodity->id) ? 'selected' : '' }}>
                                                {{ $commodity->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        لطفاً کالا را انتخاب کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="unit_id">واحد</label>
                                    <select id="unit_id" class="form-control @error('unit_id') is-invalid @enderror" name="unit_id" required>
                                        <option value="">انتخاب کنید...</option>
                                        @if(isset($selectableUnits) && $selectableUnits->count() > 0)
                                            @foreach($selectableUnits as $unit)
                                                <option value="{{ $unit->id }}" {{ (old('unit_id', $inventory->unit_id) == $unit->id) ? 'selected' : '' }}>
                                                    {{ $unit->name }} @if($unit->symbol)({{ $unit->symbol }})@endif
                                                </option>
                                            @endforeach
                                        @else
                                            {{-- Fallback: show all units if selectable units not available --}}
                                            @foreach($formData['units'] as $unit)
                                                <option value="{{ $unit->id }}" {{ (old('unit_id', $inventory->unit_id) == $unit->id) ? 'selected' : '' }}>
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">
                                        لطفاً واحد را انتخاب کنید.
                                    </div>
                                    <small class="form-text text-muted">
                                        فقط واحدهای معتبر برای این کالا نمایش داده می‌شوند.
                                    </small>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="amount">مقدار موجودی</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $inventory->amount) }}" class="form-control @error('amount') is-invalid @enderror"
                                           id="amount" placeholder="مقدار موجودی" required="">
                                    <div class="invalid-feedback">
                                        لطفاً مقدار موجودی را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="purchase_price">قیمت خرید (ریال)</label>
                                    <input type="number" step="0.01" min="0.01" name="purchase_price" value="{{ old('purchase_price', $inventory->purchase_price) }}" class="form-control @error('purchase_price') is-invalid @enderror"
                                           id="purchase_price" placeholder="قیمت خرید" required="">
                                    <div class="invalid-feedback">
                                        لطفاً قیمت خرید را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>قیمت فروش (ریال)</label>
                                    <input type="text" value="{{ $inventory->sale_price ? number_format($inventory->sale_price) : 'محاسبه نشده' }}" class="form-control" disabled>
                                    <small class="form-text text-muted">
                                        @if($inventory->commodity->type == 'product')
                                            قیمت فروش محصول
                                        @else
                                            <span class="text-warning"><i class="ti-info-circle"></i> مواد اولیه قیمت فروش ندارند</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>ارزش کل موجودی (ریال)</label>
                                    <input type="text" value="{{ $inventory->sale_price ? number_format($inventory->amount * $inventory->sale_price) : number_format($inventory->amount * $inventory->purchase_price) }}" class="form-control" disabled>
                                    <small class="form-text text-muted">
                                        @if($inventory->commodity->type == 'product')
                                            بر اساس قیمت فروش
                                        @else
                                            بر اساس قیمت خرید
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">ذخیره تغییرات</button>
                            <a href="{{ route('inventory.index') }}" class="btn btn-danger">انصراف</a>
                            <a href="{{ route('activity.index', [
                                'object_id' => $inventory->id,
                                'object_type' => class_basename($inventory),
                            ]) }}"
                               class="btn btn-dfprimary px-2 px-md-4 m-md-0">تاریخچه تغییرات</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('dashboard.inventory.partials.form-scripts')
@endsection
