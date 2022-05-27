@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزییات فعالیت</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label> کاربر انجام دهنده</label>
                                <a href="{{ route('user.show',$activity->user) }}"> <input type="text" name="name"
                                                                                           value="{{ $activity->user->full_name }}"
                                                                                           class="form-control"
                                                                                           id="exampleInputEmail111"
                                                                                           autocomplete="off" readonly></a>
                            </div>
                            <div class="form-group col-md-6">
                                <label>مرجع تغییر</label>
                                <a href="{{ route($activity->recordChange->model_detail['url'].'.show',$activity->recordChange) }}">
                                    <input type="text" name="title"
                                           value="{{ $activity->recordChange->model_detail['fa_name']}} ID = {{$activity->record_change_id}}"
                                           class="form-control" readonly
                                           id="exampleInputEmail111" placeholder="accountant"></a>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label> زمان انجام</label>
                                <input type="text" name="name"
                                       value="{{\Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($activity->created_at)) }}"
                                       class="form-control"
                                       id="exampleInputEmail111" autocomplete="off" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>نوع تغییر</label>
                                <input type="text" name="title"
                                       value="{{ $activity->action_persian_name }} @if(isset($activity->relation_persian_name))( {{ $activity->relation_persian_name }} )
                                       @endif
                                           "
                                       class="form-control" readonly
                                       id="exampleInputEmail111" placeholder="accountant">
                            </div>
                        </div>
                        @if(isset($activity->changes['new_value']))
                            @foreach($activity->changes['new_value'] as $key=>$value)
                                <div class="form-row col-md-12">
                                    <label class="form-group col-md-2">فیلد تغییر داده شده</label>
                                    <div class="form-group col-md-2">
                                        <input type="text" name="name"
                                               value="{{__('fields.'.$key)}}"
                                               class="form-control"
                                               id="exampleInputEmail111" autocomplete="off" readonly>
                                    </div>

                                    <label class="form-group col-md-2">مقدار قبلی</label>
                                    <div class="form-group col-md-2">
                                        <input type="text" name="name"
                                               value="{{$activity->changes['old_value'][$key]}}"
                                               class="form-control"
                                               id="exampleInputEmail111" autocomplete="off" readonly>
                                    </div>

                                    <label class="form-group col-md-2">مقدار جدید</label>
                                    <div class="form-group col-md-2">
                                        <input type="text" name="name"
                                               value="{{$value}}"
                                               class="form-control"
                                               id="exampleInputEmail111" autocomplete="off" readonly>
                                    </div>
                                </div>

                            @endforeach
                        @elseif(isset($activity->changes['attached']) || $activity->changes['detached'])
                            @if(($activity->changes['attached']) != null)
                                <h2> الصاق
                                    کردن {{ $activity->recordChange->model_detail['relations'][$activity->relation_name] }} </h2>
                                <div class="form-row col-md-12">
                                    @foreach($activity->changes['attached'] as $key=>$value)
                                        <div class="form-group col-md-2">
                                            <input type="text" name="name"
                                                   value="{{ $value['name'] }}"
                                                   class="form-control"
                                                   id="exampleInputEmail111" autocomplete="off" readonly>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if($activity->changes['detached'] != null )
                                <h2> حذف
                                    کردن {{ $activity->recordChange->model_detail['relations'][$activity->relation_name] }} </h2>
                                <div class="form-row col-md-12">
                                    @foreach($activity->changes['detached'] as $key=>$value)
                                        <div class="form-group col-md-2">
                                            <input type="text" name="name"
                                                   value="{{ $value['name'] }}"
                                                   class="form-control"
                                                   id="exampleInputEmail111" autocomplete="off" readonly>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{asset('js/default-assets/basic-form.js')}}"></script>
@endsection



