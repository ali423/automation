@extends('layouts.main')
@section('title', 'نمایش ویژگی')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات ویژگی</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="name">{{ __('fields.name') }}</label>
                                <input type="text" name="name" value="{{ $attribute->name }}" class="form-control" id="name" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">{{ __('fields.comment') }}</label>
                                <textarea name="description" class="form-control" id="description" rows="3" disabled>{{ $attribute->description }}</textarea>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="created_at">{{ __('fields.created_at') }}</label>
                                <input type="text" name="created_at" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($attribute->created_at)) }}" class="form-control" id="created_at" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="updated_at">{{ __('fields.updated_at') }}</label>
                                <input type="text" name="updated_at" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($attribute->updated_at)) }}" class="form-control" id="updated_at" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="creator">{{ __('fields.creator') }}</label>
                                <input type="text" name="creator" value="{{ $attribute->creator->full_name ?? 'سیستم' }}" class="form-control" id="creator" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                @can('edit_attribute', $attribute)
                                    <a href="{{ route('attribute.edit', $attribute) }}" class="btn btn-primary">ویرایش</a>
                                @endcan
                                @can('delete_attribute', $attribute)
                                    <form method="post" action="{{ route('attribute.destroy', $attribute) }}" class="d-inline w-50">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('آیا از حذف این ویژگی مطمئن هستید؟');">حذف ویژگی</button>
                                    </form>
                                @endcan
                            </div>
                            <div class="col-md-6 text-md-right">
                                @can('read_activity')
                                    <a href="{{ route('activity.index', [
                                        'object_id' => $attribute->id,
                                        'object_type' => class_basename($attribute),
                                    ]) }}"
                                       class="btn btn-dfprimary px-2 px-md-4 m-md-0">تاریخچه تغییرات</a>
                                @endcan
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
