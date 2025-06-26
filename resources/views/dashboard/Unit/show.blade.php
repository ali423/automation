@extends('layouts.main')
@section('title', 'نمایش واحد')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات واحد</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="name">{{ __('fields.name') }}</label>
                                <input type="text" name="name" value="{{ $unit->name }}" class="form-control" id="name" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="symbol">{{ __('fields.symbol') }}</label>
                                <input type="text" name="symbol" value="{{ $unit->symbol }}" class="form-control" id="symbol" autocomplete="off" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="created_at">{{ __('fields.created_at') }}</label>
                                <input type="text" name="created_at" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($unit->created_at)) }}" class="form-control" id="created_at" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="updated_at">{{ __('fields.updated_at') }}</label>
                                <input type="text" name="updated_at" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($unit->updated_at)) }}" class="form-control" id="updated_at" disabled>
                            </div>
                        </div>

                        @if($unit->commodities->count() > 0)
                            <div class="col-lg-12 mt-4">
                                <h5>{{ __('fields.related_commodities') }}</h5>
                                <div class="form-row shadow p-4 mb-3">
                                    <table class="table table-bordered">
                                        <thead class="text-center">
                                            <tr>
                                                <th>{{ __('fields.commodity.number') }}</th>
                                                <th>{{ __('fields.title') }}</th>
                                                <th>{{ __('fields.type') }}</th>
                                                <th>{{ __('fields.details') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach($unit->commodities as $commodity)
                                                <tr>
                                                    <td>{{ $commodity->number }}</td>
                                                    <td>{{ $commodity->title }}</td>
                                                    <td>{{ __('fields.commodity.types')[$commodity->type] }}</td>
                                                    <td>
                                                        <a href="{{ route('commodity.show', $commodity) }}" class=""><i class="ti-more-alt font-24"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('unit.edit', $unit) }}" class="btn btn-primary">ویرایش</a>
                                <form method="post" action="{{ route('unit.destroy', $unit) }}" class="d-inline w-50">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('آیا از حذف این واحد مطمئن هستید؟');">حذف واحد</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('activity.index', [
                                    'object_id' => $unit->id,
                                    'object_type' => class_basename($unit),
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