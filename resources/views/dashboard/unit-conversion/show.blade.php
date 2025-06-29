@extends('layouts.main')
@section('title', 'نمایش تبدیل واحد - ' . $unitConversion->commodity->title)

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات تبدیل واحد</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label>{{ __('fields.commodity.name') }}</label>
                                <input type="text" value="{{ $unitConversion->commodity->title }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label>{{ __('fields.from_unit') }}</label>
                                <input type="text" value="{{ $unitConversion->fromUnit->name }} ({{ $unitConversion->fromUnit->symbol }})" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label>{{ __('fields.to_unit') }}</label>
                                <input type="text" value="{{ $unitConversion->toUnit->name }} ({{ $unitConversion->toUnit->symbol }})" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label>{{ __('fields.conversion_rate') }}</label>
                                <input type="text" value="{{ number_format($unitConversion->conversion_rate, 2) }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label>{{ __('fields.created_at') }}</label>
                                <input type="text" value="{{ jdate($unitConversion->created_at)->format('Y/m/d') }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label>{{ __('fields.updated_at') }}</label>
                                <input type="text" value="{{ jdate($unitConversion->updated_at)->format('Y/m/d') }}" class="form-control" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('unit-conversion.edit', $unitConversion) }}" class="btn btn-primary">ویرایش</a>
                                <form method="post" action="{{ route('unit-conversion.destroy', $unitConversion) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('آیا از حذف این تبدیل اطمینان دارید؟');">{{ __('fields.delete_conversion') }}</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('activity.index', [
                                    'object_id' => $unitConversion->id,
                                    'object_type' => class_basename($unitConversion),
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