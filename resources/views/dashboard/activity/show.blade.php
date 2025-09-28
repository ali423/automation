@extends('layouts.main')
@section('title', 'نمایش فعالیت')

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
                            @if($activity->recordChange && isset($activity->recordChange->model_detail['url']))
                                <a href="{{ route($activity->recordChange->model_detail['url'] . '.show', $activity->recordChange) }}">
                                    {{ $activity->recordChange->model_detail['fa_name'] ?? '' }} ID = {{ $activity->record_change_id }}
                                </a>
                              @else
                                <span>رکورد حذف شده</span>
                              @endif
                            توسط
                            @if($activity->user)
                                <a href="{{ route('user.show', $activity->user) }}">
                                    {{ $activity->user->full_name ?? '-' }}
                                </a>
                            @else
                                <span>کاربر حذف شده</span>
                            @endif
                            در زمان
                            {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($activity->created_at)) }}
                            به صورت زیر انجام گرفته است:
                        </p>

                        <div class="card shadow m-3 rounded d-md-inline-block border border-secondary">
                            <div class="card-header text-center">
                                @if ($activity->action == 'create')
                                    <div>{{ $activity->recordChange->model_detail['fa_name'] ?? '-' }} جدید ایجاد شد</div>
                                @else
                                    {{ $activity->action_persian_name }} @if (!empty($activity->relation_persian_name))
                                        ({{ $activity->relation_persian_name }})
                                    @endif
                                @endif
                            </div>
                            @if (isset($activity->changes['custom_adjustment']) && $activity->changes['custom_adjustment'])
                                <div class="card-body">
                                    <div class="text-info">
                                        <strong>نوع تنظیم:</strong>
                                    </div>
                                    <div class="p-2">
                                        @if($activity->changes['adjustment_type'] == 'stock_adjustment')
                                            <span class="badge badge-primary">تنظیم موجودی</span>
                                            @if($activity->changes['operation'])
                                                <span class="badge badge-{{ $activity->changes['operation'] == 'add' ? 'success' : 'warning' }}">
                                                    {{ $activity->changes['operation'] == 'add' ? 'افزودن' : 'کاهش' }}
                                                </span>
                                            @endif
                                            <div class="mt-2">
                                                <strong>تغییرات:</strong>
                                                <div class="p-1">
                                                    - مقدار قبلی: <span class="text-danger">{{ number_format($activity->changes['old_amount'], 2) }}</span>
                                                </div>
                                                <div class="p-1">
                                                    - مقدار جدید: <span class="text-success">{{ number_format($activity->changes['new_amount'], 2) }}</span>
                                                </div>
                                                <div class="p-1">
                                                    - مقدار تغییر: <span class="text-info">{{ number_format($activity->changes['quantity'], 2) }}</span>
                                                </div>
                                            </div>
                                        @elseif($activity->changes['adjustment_type'] == 'price_adjustment')
                                            <span class="badge badge-warning">تنظیم قیمت فروش</span>
                                            <div class="mt-2">
                                                <strong>تغییرات:</strong>
                                                <div class="p-1">
                                                    - قیمت قبلی: <span class="text-danger">{{ number_format($activity->changes['old_price']) }} ریال</span>
                                                </div>
                                                <div class="p-1">
                                                    - قیمت جدید: <span class="text-success">{{ number_format($activity->changes['new_price']) }} ریال</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($activity->changes['reason'] && $activity->changes['reason'] !== 'بدون دلیل')
                                    <div class="card-body border-top">
                                        <div class="text-info">
                                            <strong>دلیل تغییر:</strong>
                                        </div>
                                        <div class="p-2">
                                            <span class="text-dark">{{ $activity->changes['reason'] }}</span>
                                        </div>
                                    </div>
                                @endif
                            @elseif (isset($activity->changes['new_value']) && !empty($activity->changes['new_value']))
                                @foreach ($activity->changes['new_value'] as $key => $value)
                                    <div class="card-body">
                                        <div class="">
                                            {{ __('fields.' . $key) }}:
                                        </div>
                                        <div class="p-1">
                                            - مقدار قبلی:
                                            <span class="text-danger">
                                                {{ $activity->changes['old_value'][$key] ?? '(بدون مقدار)' }}
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
                                                <div class="text-success">+ {{ $value['name'] ?? $value['title'] ?? null}}</div>
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
                                                    <div class="text-success">+ {{ $value['name'] ?? $value['title']?? null }}</div>
                                                    @if(isset($value['pivots']))
                                                        @foreach($value['pivots'] as $key_2=>$value_2)
                                                            <div class="text-primary">* {{ $activity->pivotName($key_2) }}  = {{ $value_2 }}</div>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @if (!empty($activity->changes['pivot_update']))
                                        <div>
                                            ویرایش گزینه های  {{ $activity->relation_persian_name }}
                                        </div>
                                    @foreach($activity->changes['pivot_update'] as $key=>$value)
                                    <div class="card-body">
                                        <div>
                                            ویرایش  {{ $value['relation_name'] }}
                                        </div>
                                        <div class="p-2">
                                            @foreach ($value['changes'] as $key_2 => $value_2)
                                                <div class="text-dark"> عنوان : {{ $value['title'] }}</div>
                                                <div class="text-success">* {{ $value_2['pivot_title'] }} = {{ $value_2['new_amount'] }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                        @endforeach
                                @endif
                                @if (empty($activity->changes['attached']) && empty($activity->changes['updated']) && empty($activity->changes['pivot_update']))
                                    <div class="card-body">
                                        <div class="text-muted">
                                            هیچ تغییر مشخصی ثبت نشده است.
                                        </div>
                                    </div>
                                @endif


                            @endif

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
