@extends('layouts.main')
@section('title', 'نمایش انبار')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات انبار</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="fields"> {{ __('fields.title') }}</label>
                                <input type="text" name="fields" value="{{ $warehouse->title }}" class="form-control"
                                       id="exampleInputEmail111" autocomplete="off"
                                       disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.type') }}</label>
                                <input type="text" name="type" value="{{ __('fields.warehouse.types') [$warehouse->type] }}" class="form-control"
                                       id="exampleInputEmail111" autocomplete="off"
                                       disabled>
                            </div>
                                <div class="form-group col-md-4">
                                    <label for="capacity"> {{ __('fields.capacity') }}</label>
                                    <input type="text" name="capacity" value="{{ number_format($warehouse->capacity) }}" class="form-control"
                                           id="exampleInputEmail111" autocomplete="off"
                                           disabled>
                                </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.status') }}</label>
                                <input type="text" name="name" value="{{ __('fields.warehouse.status')[$warehouse->status] }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.status') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.created_at') }}</label>
                                <input type="text" name="name"
                                       value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($warehouse->created_at)) }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.creator') }}</label>
                                <input type="text" name="name"
                                       @if (isset($warehouse->creator_user)) value="{{ $warehouse->creator_user->full_name }}"
                                       @else
                                       value="سیستم" @endif
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('warehouse.edit', $warehouse) }}" class="btn btn-primary">ویرایش</a>
                                <form method="post" action="{{ route('warehouse.destroy', $warehouse) }}" class="d-inline w-50">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('آیا از حذف این انبار مطمئن هستید؟');">حذف انبار</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('activity.index', [
                                    'object_id' => $warehouse->id,
                                    'object_type' => class_basename($warehouse),
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
