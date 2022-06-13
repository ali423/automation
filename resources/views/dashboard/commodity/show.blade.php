@extends('layouts.main')
@section('title', 'داشبورد')

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
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.name') }}</label>
                                <input type="text" name="name" value="{{ $commodity->title }}" class="form-control"
                                       id="exampleInputEmail111" autocomplete="off"
                                       disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.type') }}</label>
                                <input type="text" name="type" value="{{ __('fields.commodity.types') [$commodity->type] }}" class="form-control"
                                       id="exampleInputEmail111" autocomplete="off"
                                       disabled>
                            </div>
                            @if(!empty($commodity->sales_price))
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail111"> {{ __('fields.sales_price') }}</label>
                                    <input type="text" name="amount" value="{{ number_format($commodity->sales_price) }}" class="form-control"
                                           id="exampleInputEmail111" autocomplete="off"
                                           disabled>
                                </div>
                                @endif
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.commodity.number') }}</label>
                                <input type="text" name="number" value="{{$commodity->number }}"
                                       class="form-control" id="exampleInputEmail111"
                                      disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.created_at') }}</label>
                                <input type="text" name="name"
                                       value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($commodity->created_at)) }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail111"> {{ __('fields.creator') }}</label>
                                <input type="text" name="name"
                                       @if (isset($commodity->creator_user)) value="{{ $commodity->creator_user->full_name }}"
                                       @else
                                       value="سیستم" @endif
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                        </div>

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
