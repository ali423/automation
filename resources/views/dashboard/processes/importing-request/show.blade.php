@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزییات درخواست ورود کالا به انبار</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.created_at') }}</label>
                                <input type="text" name="name"
                                       value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) }}"
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.created_at') }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.creator') }}</label>
                                <input type="text" name="name"
                                       @if (isset($request->creator_user)) value="{{ $request->creator_user->full_name }}"
                                       @else
                                       value="سیستم" @endif
                                       class="form-control" id="exampleInputEmail111"
                                       placeholder="{{ __('fields.creator') }}" autocomplete="off" disabled>
                            </div>
                        </div>
                        @foreach($request->commodities as $commodity)
                        <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                            <div class="form-group col-md-6">
                                <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
                                <select id="commodity_id" class="form-control" name="commodity_id[0]" required>
                                        <option value="{{ $commodity->id }}">{{ $commodity->title }}</option>
                                </select>
                                <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="unit"> {{ __('fields.unit') }}</label>
                                <input type="text" value="{{__('fields.commodity.units')[$commodity->pivot->unit]}}" id="unit" name="unit" class="form-control"
                                       disabled>
                                <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="warehouse_id"> {{ __('fields.warehouse.name') }}</label>
                                <select id="warehouse_id" class="form-control" name="warehouse_id[0]" required>
                                    @foreach ($warehouses as $warehouse)
                                        @if($warehouse->id == $commodity->pivot->warehouses_id)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->title }}
                                        </option>
                                            @endif
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">{{ __('fields.warehouse.name') }} را انتخاب کنید.</div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                                <input type="number" value="{{$commodity->pivot->amount}}" id="amount" min="1" name="amount[0]" class="form-control"
                                       autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]"  disabled>
                                <div class="invalid-feedback">
                                    لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @foreach($request->comments as $comment)
                            <div class="form-group mb-20">
                                <label for="comment"> {{$comment->user->full_name}} در تاریخ :  {{  \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($comment->created_at)) }}</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment" rows="6" disabled>{{$comment->body}}</textarea>
                            </div>
                            @endforeach
                        <div class="col-xl-12 height-card box-margin">
                            <div class="card">
                                <div class="card-body">
                                    <div class="bg-transparent d-flex align-items-center justify-content-between">
                                        <div class="widgets-card-title">
                                            <h5 class="card-title">فایل ضمیمه شده</h5>
                                        </div>
                                    </div>
                                @foreach($request->files as $file)
                                    <!-- Single Download File -->
                                    <div class="widget-download-file d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center mr-3">
                                            <div class="download-file-icon mr-3">
                                                <img src="{{asset('img/filemanager-img/1.png')}}" alt="">
                                            </div>
                                            <div class="user-text-table">
                                                <h6 class="d-inline-block font-15 mb-0">{{$file->name}}</h6>
                                                <p class="mb-0"> {{$file->user->full_name}} در تاریخ :  {{  \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($file->created_at)) }}</p>
                                            </div>
                                        </div>
                                        <a href="{{asset(str_replace('public', 'storage', $file->source))}}" download="proposed_file_name" class="download-link badge badge-primary badge-pill">دانلود</a>
                                    </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                            <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('importing-request.edit', $request) }}" class="btn btn-primary">ویرایش</a>
                                <form method="post" action="{{ route('importing-request.destroy', $request) }}" class="d-inline w-50">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('آیا از حذف این درخواست مطمئن هستید؟');">حذف انبار</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('activity.index', [
                                    'object_id' => $request->id,
                                    'object_type' => class_basename($request),
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
