@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزییات فعالیت</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <p>
                            تغییرات مورد نظر در ردیف
                            ( <a
                                href="{{ route($activity->recordChange->model_detail['url'] . '.show', $activity->recordChange) }}">
                                {{ $activity->recordChange->model_detail['fa_name'] }} ID =
                                {{ $activity->record_change_id }}
                            </a> )
                            توسط
                            <a href="{{ route('user.show', $activity->user) }}">
                                {{ $activity->user->full_name }}
                            </a>
                            در زمان
                            {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($activity->created_at)) }}
                            به صورت زیر انجام گرفته است:
                        </p>

                        <div class="card shadow m-3 rounded d-md-inline-block border border-secondary">
                            <div class="card-header text-center">
                                @if ($activity->action == 'create')
                                    <div>{{ $activity->recordChange->model_detail['fa_name'] }} جدید ایجاد شد</div>
                                @else
                                    {{ $activity->action_persian_name }} @if (isset($activity->relation_persian_name))
                                        ({{ $activity->relation_persian_name }})
                                    @endif
                                @endif
                            </div>
                            @if (isset($activity->changes['new_value']))
                                @foreach ($activity->changes['new_value'] as $key => $value)
                                    <div class="card-body">
                                        <div class="">
                                            {{ __('fields.' . $key) }}:
                                        </div>
                                        <div class="p-1">
                                            - مقدار قبلی:
                                            <span class="text-danger">
                                                {{ $activity->changes['old_value'][$key] }}
                                            </span>
                                        </div>
                                        <div class="p-1">
                                            - مقدار جدید:
                                            <span class="text-success">
                                                {{ $value }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @if (!empty($activity->changes['attached']))
                                    <div class="card-body">
                                        <div>
                                            افزودن {{ $activity->relation_persian_name }}
                                        </div>
                                        <div class="p-2">
                                            @foreach ($activity->changes['attached'] as $key => $value)
                                                <div class="text-success">+ {{ $value['name'] ?? $value['title'] }}</div>
                                            @if(isset($value['pivots']))
                                               @foreach($value['pivots'] as $key_2=>$value_2)
                                                    <div class="text-primary">* {{ $activity->pivotName($key_2) }}  = {{ $value_2 }}</div>
                                                @endforeach
                                                @endif
                                                    @endforeach
                                        </div>
                                    </div>
                                @endif
                                    @if (!empty($activity->changes['updated']))
                                        <div class="card-body">
                                            <div>
                                                ویرایش گزینه های  {{ $activity->relation_persian_name }}
                                            </div>
                                            <div class="p-2">
                                                @foreach ($activity->changes['updated'] as $key => $value)
                                                    <div class="text-success">+ {{ $value['name'] ?? $value['title'] }}</div>
                                                    @if(isset($value['pivots']))
                                                        @foreach($value['pivots'] as $key_2=>$value_2)
                                                            <div class="text-primary">* {{ $activity->pivotName($key_2) }}  = {{ $value_2 }}</div>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @if (!empty($activity->changes['detached']))
                                    <div class="card-body">
                                        <div>
                                            حذف {{ $activity->relation_persian_name }}
                                        </div>
                                        <div class="p-2">
                                            @foreach ($activity->changes['detached'] as $key => $value)
                                                <div class="text-danger">- {{ $value['name'] }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- <div class="form-row">
                            <div class="form-group col-md-6">
                                <label> کاربر انجام دهنده</label>
                                <a href="{{ route('user.show', $activity->user) }}" class="btn btn-primary">
                                    {{ $activity->user->full_name }}
                                </a>
                            </div>
                            <div class="form-group col-md-6">
                                <label>مرجع تغییر</label>
                                <a
                                    href="{{ route($activity->recordChange->model_detail['url'] . '.show', $activity->recordChange) }}">
                                    <input type="text" name="title"
                                        value="{{ $activity->recordChange->model_detail['fa_name'] }} ID = {{ $activity->record_change_id }}"
                                        class="form-control" id="exampleInputEmail111" placeholder="accountant" readonly
                                        disabled>
                                </a>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label> زمان انجام</label>
                                <input type="text" name="name"
                                    value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($activity->created_at)) }}"
                                    class="form-control" id="exampleInputEmail111" autocomplete="off" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>نوع تغییر</label>
                                <input type="text" name="title" value="{{ $activity->action_persian_name }} @if (isset($activity->relation_persian_name)) ( {{ $activity->relation_persian_name }} ) @endif
                                                                                                   " class="form-control"
                                    readonly id="exampleInputEmail111" placeholder="accountant">
                            </div>
                        </div>
                        @if (isset($activity->changes['new_value']))
                            @foreach ($activity->changes['new_value'] as $key => $value)
                                <div class="form-row col-md-12">
                                    <label class="form-group col-md-2">فیلد تغییر داده شده</label>
                                    <div class="form-group col-md-2">
                                        <input type="text" name="name" value="{{ __('fields.' . $key) }}"
                                            class="form-control" id="exampleInputEmail111" autocomplete="off" readonly>
                                    </div>

                                    <label class="form-group col-md-2">مقدار قبلی</label>
                                    <div class="form-group col-md-2">
                                        <input type="text" name="name"
                                            value="{{ $activity->changes['old_value'][$key] }}" class="form-control"
                                            id="exampleInputEmail111" autocomplete="off" readonly>
                                    </div>

                                    <label class="form-group col-md-2">مقدار جدید</label>
                                    <div class="form-group col-md-2">
                                        <input type="text" name="name" value="{{ $value }}" class="form-control"
                                            id="exampleInputEmail111" autocomplete="off" readonly>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @if (!empty($activity->changes['attached']))
                                <h2> الصاق
                                    کردن {{ $activity->relation_persian_name }} </h2>
                                <div class="form-row col-md-12">
                                    @foreach ($activity->changes['attached'] as $key => $value)
                                        <div class="form-group col-md-2">
                                            <input type="text" name="name" value="{{ $value['name'] }}"
                                                class="form-control" id="exampleInputEmail111" autocomplete="off"
                                                readonly>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if (!empty($activity->changes['detached']))
                                <h2> حذف
                                    کردن {{ $activity->relation_persian_name }} </h2>
                                <div class="form-row col-md-12">
                                    @foreach ($activity->changes['detached'] as $key => $value)
                                        <div class="form-group col-md-2">
                                            <input type="text" name="name" value="{{ $value['name'] }}"
                                                class="form-control" id="exampleInputEmail111" autocomplete="off"
                                                readonly>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif --}}
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
