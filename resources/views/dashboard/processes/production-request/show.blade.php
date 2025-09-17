@extends('layouts.main')
@section('title', 'جزئیات درخواست تولید')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزئیات درخواست تولید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.status') }}</label>
                                <input type="text" value="{{ $request->status_text }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.production-request.number') }}</label>
                                <input type="text" value="{{ $request->number }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.production-request.product_id') }}</label>
                                <input type="text" value="{{ $request->product ? $request->product->title : 'نامشخص' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.production-request.production_amount') }}</label>
                                <input type="text" value="{{ number_format($request->production_amount, 2) }} {{ $request->unit ? $request->unit->name : '' }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.unit') }}</label>
                                <input type="text" value="{{ $request->unit ? $request->unit->name . ' (' . $request->unit->symbol . ')' : 'نامشخص' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.created_at') }}</label>
                                <input type="text" value="{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i', strtotime($request->created_at)) }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>{{ __('fields.creator') }}</label>
                                <input type="text" value="{{ isset($request->creator_user) ? $request->creator_user->full_name : 'سیستم' }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label>کل هزینه (ریال)</label>
                                <input type="text" value="{{ number_format($request->total_cost) }}" class="form-control" disabled>
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

                        <!-- Materials Section -->
                        <div class="col-lg-12 mt-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fa fa-cubes mr-2"></i>
                                        مواد اولیه
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @include('dashboard.processes.production-request.partials.materials-table')
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        @include('dashboard.processes.production-request.partials.financial-summary')

                        <!-- Comments Section -->
                        @if($request->comments->count() > 0)
                            <div class="col-lg-12 mt-4">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fa fa-comments mr-2"></i>
                                            نظرات و توضیحات
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($request->comments as $comment)
                                            <div class="card mb-3 border-left-success">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fa fa-user-circle fa-2x text-success mr-2"></i>
                                                                <div>
                                                                    <strong>{{ $comment->user->full_name }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i', strtotime($comment->created_at)) }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <p class="mb-0">{{ $comment->body }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Files Section -->
                        @if($request->files->count() > 0)
                            <div class="col-lg-12 mt-4">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fa fa-paperclip mr-2"></i>
                                            فایل‌های ضمیمه
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($request->files as $file)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="card h-100 border-primary">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fa fa-file fa-2x text-primary mr-3"></i>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1">{{ $file->name }}</h6>
                                                                    <small class="text-muted">
                                                                        {{ $file->user->full_name }} - 
                                                                        {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($file->created_at)) }}
                                                                    </small>
                                                                </div>
                                                                <a href="{{ asset(str_replace('public', 'storage', $file->source)) }}" 
                                                                   download="{{ $file->name }}"
                                                                   class="btn btn-sm btn-outline-primary">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        @include('dashboard.processes.production-request.partials.action-buttons')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection 