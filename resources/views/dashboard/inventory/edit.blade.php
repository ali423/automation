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
                                        @foreach($formData['units'] as $unit)
                                            <option value="{{ $unit->id }}" {{ (old('unit_id', $inventory->unit_id) == $unit->id) ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        لطفاً واحد را انتخاب کنید.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="amount">مقدار موجودی</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $inventory->amount) }}" class="form-control @error('amount') is-invalid @enderror"
                                           id="amount" placeholder="مقدار موجودی" required="">
                                    <div class="invalid-feedback">
                                        لطفاً مقدار موجودی را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="purchase_price">قیمت خرید (تومان)</label>
                                    <input type="number" step="0.01" min="0.01" name="purchase_price" value="{{ old('purchase_price', $inventory->purchase_price) }}" class="form-control @error('purchase_price') is-invalid @enderror"
                                           id="purchase_price" placeholder="قیمت خرید" required="">
                                    <div class="invalid-feedback">
                                        لطفاً قیمت خرید را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>قیمت فروش (تومان)</label>
                                    <input type="text" value="{{ $inventory->sale_price ? number_format($inventory->sale_price) : 'محاسبه نشده' }}" class="form-control" disabled>
                                    <small class="form-text text-muted">
                                        @if($inventory->commodity->type == 'product')
                                            قیمت بر اساس درصد سود کالا محاسبه می‌شود
                                        @else
                                            مواد اولیه قیمت فروش ندارند
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="active">وضعیت</label>
                                    <select name="active" id="active" class="form-control @error('active') is-invalid @enderror">
                                        <option value="1" {{ (old('active', $inventory->active) == 1) ? 'selected' : '' }}>فعال</option>
                                        <option value="0" {{ (old('active', $inventory->active) == 0) ? 'selected' : '' }}>غیرفعال</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        لطفاً وضعیت را انتخاب کنید.
                                    </div>
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
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection 