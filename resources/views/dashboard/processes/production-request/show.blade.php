@extends('layouts.main')
@section('title', 'جزئیات درخواست تولید')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزئیات درخواست تولید</h4>
                
                <!-- Basic Information Section -->
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.status') }}</label>
                                <input type="text" value="{{ $request->status_text }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.production-request.number') }}</label>
                                <input type="text" value="{{ $request->number ?? 'نامشخص' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.created_at') }}</label>
                                <input type="text" value="{{ $request->created_at ? \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request->created_at)) : 'نامشخص' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.creator') }}</label>
                                <input type="text" value="{{ isset($request->creator_user) ? $request->creator_user->full_name : 'سیستم' }}" class="form-control" disabled>
                            </div>
                        </div>
                        
                        @if($request->description)
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-12">
                                    <label>{{ __('fields.production-request.description') }}</label>
                                    <input type="text" value="{{ $request->description }}" class="form-control" disabled>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Product Information Section -->
                @if(isset($summary['main_product']) && $summary['main_product'])
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="ti-package"></i> محصول تولیدی
                                    </h5>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>نام محصول</label>
                                            <input type="text" value="{{ $summary['main_product']->title }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>مقدار تولید</label>
                                            <input type="text" value="{{ number_format($summary['production_amount'], 2) }} {{ $summary['production_unit'] ? $summary['production_unit']->name : '' }}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>شماره محصول</label>
                                            <input type="text" value="{{ $summary['main_product']->number }}" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Input Materials Section -->
                @if($request->inputMaterials && $request->inputMaterials->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti-layers"></i> مواد اولیه مورد نیاز
                                        @if(isset($summary['all_materials_available']))
                                            @if($summary['all_materials_available'])
                                                <span class="badge badge-success ml-2">
                                                    <i class="fa fa-check"></i> تمام مواد موجود است
                                                </span>
                                            @else
                                                <span class="badge badge-warning ml-2">
                                                    <i class="fa fa-exclamation-triangle"></i> 
                                                    {{ $summary['materials_with_shortage'] }} ماده کمبود دارد
                                                </span>
                                            @endif
                                        @endif
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($summary['input_materials_with_inventory']))
                                        @foreach($summary['input_materials_with_inventory'] as $materialData)
                                            @php $material = $materialData['material']; @endphp
                                            <div class="form-row shadow p-4 m-3 {{ $materialData['is_sufficient'] ? 'border-left-success' : 'border-left-warning' }}">
                                                <div class="showbarrel">
                                                    <i class="fa fa-database"></i>
                                                    <div>
                                                        <span>موجودی فعلی</span>
                                                        <span class="font-weight-bold">
                                                            {{ number_format($materialData['current_stock'], 2) }} {{ $material->unit ? $material->unit->name : 'نامشخص' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>نام ماده</label>
                                                    <input type="text" value="{{ $material->title }}" class="form-control" disabled>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>واحد</label>
                                                    <input type="text" value="{{ $material->unit ? $material->unit->name : 'نامشخص' }}" class="form-control" disabled>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>مقدار مورد نیاز</label>
                                                    <input type="text" value="{{ number_format($materialData['required_amount'], 2) }}" class="form-control" disabled>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>وضعیت موجودی</label>
                                                    <input type="text" value="{{ number_format($materialData['current_stock'], 2) }}" class="form-control {{ $materialData['is_sufficient'] ? 'text-success' : 'text-danger' }}" disabled>
                                                    @if($materialData['is_sufficient'])
                                                        @if($materialData['surplus'] > 0)
                                                            <small class="text-muted">مازاد: {{ number_format($materialData['surplus'], 2) }}</small>
                                                        @else
                                                            <small class="text-muted">دقیق</small>
                                                        @endif
                                                    @else
                                                        <small class="text-danger">کمبود: {{ number_format($materialData['shortage'], 2) }}</small>
                                                    @endif
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>هزینه واحد</label>
                                                    <input type="text" value="{{ number_format($material->pivot->unit_cost ?? 0) }} تومان" class="form-control" disabled>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach($request->inputMaterials as $material)
                                            <div class="form-row shadow p-4 m-3">
                                                <div class="form-group col-md-6">
                                                    <label>نام ماده</label>
                                                    <input type="text" value="{{ $material->title }}" class="form-control" disabled>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>واحد</label>
                                                    <input type="text" value="{{ $material->unit ? $material->unit->name : 'نامشخص' }}" class="form-control" disabled>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>مقدار مورد نیاز</label>
                                                    <input type="text" value="{{ number_format($material->pivot->amount, 2) }}" class="form-control" disabled>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>وضعیت موجودی</label>
                                                    <input type="text" value="نامشخص" class="form-control text-muted" disabled>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    <!-- Inventory Summary -->
                                    @if(isset($summary['input_materials_with_inventory']))
                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="alert alert-info">
                                                        <h6><i class="fa fa-info-circle"></i> خلاصه موجودی:</h6>
                                                        <ul class="mb-0">
                                                            <li>مواد کافی: {{ collect($summary['input_materials_with_inventory'])->where('is_sufficient', true)->count() }}</li>
                                                            <li>مواد ناکافی: {{ collect($summary['input_materials_with_inventory'])->where('is_sufficient', false)->count() }}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    @if(!$summary['all_materials_available'])
                                                        <div class="alert alert-warning">
                                                            <h6><i class="fa fa-exclamation-triangle"></i> هشدار:</h6>
                                                            <p class="mb-0">
                                                                برخی از مواد اولیه به مقدار کافی در انبار موجود نیست. 
                                                                با این حال، می‌توانید درخواست را تایید کنید.
                                                            </p>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-success">
                                                            <h6><i class="fa fa-check-circle"></i> وضعیت مطلوب:</h6>
                                                            <p class="mb-0">
                                                                تمام مواد اولیه مورد نیاز در انبار موجود است و امکان تولید وجود دارد.
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Financial Summary Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ti-money"></i> خلاصه مالی
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted">هزینه کل مواد اولیه</h6>
                                                <h4 class="text-primary">{{ number_format($request->total_input_cost ?? 0) }}</h4>
                                                <small class="text-muted">تومان</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted">ارزش کل محصولات</h6>
                                                <h4 class="text-info">{{ number_format($request->total_output_value ?? 0) }}</h4>
                                                <small class="text-muted">تومان</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted">سود/زیان</h6>
                                                <h4 class="{{ ($request->profit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($request->profit ?? 0) }}
                                                </h4>
                                                <small class="text-muted">تومان</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted">درصد سود</h6>
                                                @if(($request->total_input_cost ?? 0) > 0)
                                                    @php
                                                        $profitPercentage = (($request->profit ?? 0) / ($request->total_input_cost ?? 1)) * 100;
                                                    @endphp
                                                    <h4 class="{{ $profitPercentage >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($profitPercentage, 1) }}%
                                                    </h4>
                                                @else
                                                    <h4 class="text-muted">-</h4>
                                                @endif
                                                <small class="text-muted">درصد</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                @if($request->comments && $request->comments->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">توضیحات</h5>
                                </div>
                                <div class="card-body">
                                    @foreach ($request->comments as $comment)
                                        <div class="comment-item border-bottom pb-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>{{ $comment->user->full_name ?? 'سیستم' }}</strong>
                                                    <small class="text-muted d-block">
                                                        {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($comment->created_at)) }}
                                                    </small>
                                                </div>
                                            </div>
                                            <p class="mb-0 mt-2">{{ $comment->body }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Files Section -->
                @if($request->files && $request->files->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">فایل ضمیمه شده</h5>
                                </div>
                                <div class="card-body">
                                    @foreach ($request->files as $file)
                                        <div class="widget-download-file d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center mr-3">
                                                <div class="download-file-icon mr-3">
                                                    <img src="{{ asset('img/filemanager-img/1.png') }}" alt="">
                                                </div>
                                                <div class="user-text-table">
                                                    <h6 class="d-inline-block font-15 mb-0">{{ $file->name ?? 'نامشخص' }}</h6>
                                                    <p class="mb-0"> {{ $file->user->full_name ?? 'سیستم' }} در تاریخ :
                                                        {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($file->created_at)) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ asset(str_replace('public', 'storage', $file->source ?? '')) }}"
                                                download="proposed_file_name"
                                                class="download-link badge badge-primary badge-pill p-2 font-16"><i
                                                    class="ti-download"></i></a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Activities Section -->
                @if($request->activities && $request->activities->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('fields.activities') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        @foreach($request->activities as $activity)
                                            <div class="timeline-item">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <div class="d-flex justify-content-between">
                                                        <strong>{{ $activity->user->full_name ?? 'سیستم' }}</strong>
                                                        <small>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i', strtotime($activity->created_at)) }}</small>
                                                    </div>
                                                    <p class="mb-0 mt-1">
                                                        <strong>{{ $activity->action_persian_name }}</strong>
                                                        @if($activity->description)
                                                            - {{ $activity->description }}
                                                        @endif
                                                    </p>
                                                    @if($activity->relation_name)
                                                        <small class="text-muted">
                                                            {{ $activity->relation_persian_name }}: {{ $activity->relation_name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        @if($request->is_editable)
                            <a href="{{ route('production-request.edit', $request) }}" class="btn btn-primary">ویرایش درخواست</a>
                        @endif
                        @if($request->is_deletable)
                            <form method="post" action="{{ route('production-request.destroy', $request) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('آیا از حذف این درخواست مطمئن هستید؟');">حذف درخواست</button>
                            </form>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-right">
                        @if ($request->status == 'awaiting_approval')
                            <a href="{{ route('approval.production', $request) }}" class="btn btn-success">
                                <i class="fa fa-check"></i> تایید درخواست
                                @if(isset($summary['all_materials_available']) && !$summary['all_materials_available'])
                                    <span class="badge badge-warning ml-1">هشدار</span>
                                @endif
                            </a>
                            <a href="{{ route('reject.production', $request) }}" class="btn btn-danger">رد درخواست</a>
                        @endif
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
@endsection

@section('page_scripts')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #007bff;
        }
        .timeline-content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .comment-item:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
        }
        
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }
        .border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }
        .badge {
            font-size: 0.75em;
        }
        .showbarrel {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .showbarrel i {
            font-size: 1.5em;
            margin-right: 10px;
            color: #007bff;
        }
        .showbarrel div {
            display: flex;
            flex-direction: column;
        }
        .showbarrel span:first-child {
            font-size: 0.8em;
            color: #6c757d;
            margin-bottom: 2px;
        }
        .showbarrel span:last-child {
            font-weight: 600;
            font-size: 1.1em;
        }
    </style>
@endsection 