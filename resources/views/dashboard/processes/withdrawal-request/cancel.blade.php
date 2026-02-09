@extends('layouts.main')
@section('title', 'لغو درخواست فروش')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">لغو درخواست فروش</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('cancel.withdrawal.submit', $request->id) }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>دلیل لغو (اختیاری)</label>
                                    <textarea name="reason" class="form-control" rows="4" placeholder="در صورت نیاز دلیل لغو را وارد کنید.">{{ old('reason') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-warning">لغو و بازگشت موجودی</button>
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
