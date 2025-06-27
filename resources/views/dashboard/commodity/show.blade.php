@extends('layouts.main')
@section('title', 'نمایش کالا')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات کالا</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.title') }}</label>
                                <input type="text" value="{{ $commodity->title }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.type') }}</label>
                                <input type="text" value="{{ __('fields.commodity.types')[$commodity->type] }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.unit') }}</label>
                                <input type="text" value="{{ $commodity->unit ? $commodity->unit->name . ' (' . $commodity->unit->symbol . ')' : '-' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                @if(!empty($commodity->sales_price))
                                    <label>{{ __('fields.sales_price') }} هر {{ $commodity->unit ? $commodity->unit->symbol : '' }}</label>
                                    <input type="text" value="{{ number_format($commodity->sales_price) }}" class="form-control" disabled>
                                @elseif(!empty($commodity->purchase_price))
                                    <label>{{ __('fields.purchase_price') }} هر {{ $commodity->unit ? $commodity->unit->symbol : '' }}</label>
                                    <input type="text" value="{{ number_format($commodity->purchase_price) }}" class="form-control" disabled>
                                @else
                                    <label>&nbsp;</label>
                                    <input type="text" class="form-control" disabled>
                                @endif
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.commodity.number') }}</label>
                                <input type="text" value="{{ $commodity->number }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.warning_limit') }} ({{ $commodity->unit ? $commodity->unit->symbol : '' }})</label>
                                <input type="text" value="{{ number_format($commodity->warning_limit) }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.created_at') }}</label>
                                <input type="text" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($commodity->created_at)) }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.creator') }}</label>
                                <input type="text" value="{{ isset($commodity->creator_user) ? $commodity->creator_user->full_name : 'سیستم' }}" class="form-control" disabled>
                            </div>
                        </div>

                        @if($commodity->type == 'product')
                            <div id="product_formul" class="col-lg-12">
                                <p>فرمول ساخت برای صد {{ $commodity->unit ? $commodity->unit->symbol : 'واحد' }} فراورده</p>
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    @foreach ($materials as $material)
                                        <div class="form-group col-md-5">
                                            <label>{{ __('fields.commodity.material_type') }}</label>
                                            <input type="text" value="{{ $material->title }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label>{{ __('fields.commodity.material_amount') }}</label>
                                            <input type="number" step="0.01" value="{{ $material->pivot->percentage }}" class="form-control" disabled>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('commodity.edit', $commodity) }}" class="btn btn-primary">ویرایش</a>
                                <form method="post" action="{{ route('commodity.destroy', $commodity) }}" class="d-inline w-50">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('آیا از حذف این کالا مطمئن هستید؟');">حذف کالا</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('activity.index', [
                                    'object_id' => $commodity->id,
                                    'object_type' => class_basename($commodity),
                                ]) }}"
                                   class="btn btn-dfprimary px-2 px-md-4 m-md-0">تاریخچه تغییرات</a>
                            </div>
                        </div>

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
