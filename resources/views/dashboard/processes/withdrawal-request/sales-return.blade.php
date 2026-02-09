@extends('layouts.main')
@section('title', 'برگشت از فروش')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">برگشت از فروش - درخواست شماره {{ $request->number }}</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('sales-return.withdrawal.submit', $request->id) }}" class="needs-validation" novalidate>
                            @csrf
                            
                            <div class="alert alert-info">
                                <strong>راهنما:</strong> مقدار برگشتی هر کالا را وارد کنید. در صورتی که کالایی برگشت نداشته، مقدار آن را صفر بگذارید.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>کالا</th>
                                            <th>مقدار اصلی</th>
                                            <th>واحد اصلی</th>
                                            <th>قبلاً برگشت داده شده</th>
                                            <th>مقدار برگشتی جدید</th>
                                            <th>واحد</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($request->commodities as $index => $commodity)
                                            <tr>
                                                <td>
                                                    {{ $commodity->title }}
                                                    <input type="hidden" name="commodity_id[]" value="{{ $commodity->id }}">
                                                </td>
                                                <td>
                                                    {{ $commodity->pivot->amount }}
                                                </td>
                                                <td>
                                                    {{ $commodity->pivot->unit ? $commodity->pivot->unit->name : '-' }}
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary">
                                                        {{ $commodity->already_returned ?? 0 }} {{ $commodity->unit->symbol ?? '' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           name="return_amount[]" 
                                                           class="form-control" 
                                                           value="{{ old('return_amount.' . $index, 0) }}"
                                                           step="0.01"
                                                           min="0"
                                                           placeholder="0">
                                                </td>
                                                <td>
                                                    <select name="unit_id[]" class="form-control">
                                                        @foreach($commodity->selectable_units as $unit)
                                                            <option value="{{ $unit['id'] }}" 
                                                                {{ ($unit['id'] == $commodity->pivot->unit_id) ? 'selected' : '' }}>
                                                                {{ $unit['name'] }} ({{ $unit['symbol'] }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>دلیل برگشت (اختیاری)</label>
                                    <textarea name="reason" class="form-control" rows="3" placeholder="مثلاً: کالای معیوب، سفارش اشتباه، تغییر نظر مشتری">{{ old('reason') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-info">ثبت برگشت و بازگشت موجودی</button>
                                <a href="{{ route('withdrawal-request.show', $request) }}" class="btn btn-secondary">بازگشت</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection
