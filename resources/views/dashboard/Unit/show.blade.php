@extends('layouts.main')
@section('title', 'جزئیات واحد')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">جزئیات واحد</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-4">
                                <label for="name">نام</label>
                                <input type="text" name="name" value="{{ $unit->name }}" class="form-control" id="name" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="symbol">نماد</label>
                                <input type="text" name="symbol" value="{{ $unit->symbol }}" class="form-control" id="symbol" disabled>
                            </div>
                        </div>
                        <div class="form-row col-md-12">
                            <div class="form-group col-md-6">
                                <label for="created_at">تاریخ ایجاد</label>
                                <input type="text" name="created_at" value="{{ $unit->created_at->format('Y-m-d H:i:s') }}" class="form-control" id="created_at" disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="updated_at">آخرین بروزرسانی</label>
                                <input type="text" name="updated_at" value="{{ $unit->updated_at->format('Y-m-d H:i:s') }}" class="form-control" id="updated_at" disabled>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <a href="{{ route('unit.edit', $unit) }}" class="btn btn-primary">ویرایش</a>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('unit.index') }}" class="btn btn-secondary">بازگشت به لیست</a>
                            </div>
                        </div>
                        @if($unit->commodities->count() > 0)
                            <h4 class="mt-4">کالاهای مرتبط</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>شناسه</th>
                                        <th>نام</th>
                                        <th>نوع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unit->commodities as $commodity)
                                        <tr>
                                            <td>{{ $commodity->id }}</td>
                                            <td>{{ $commodity->name }}</td>
                                            <td>{{ $commodity->type }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
@endsection