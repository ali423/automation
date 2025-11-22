@extends('layouts.main')
@section('title', 'تایید درخواست فروش - اطلاعات راننده')

@section('page_styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">تایید درخواست فروش - اطلاعات راننده</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('approval.withdrawal.submit', $request->id) }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>نام راننده</label>
                                    <input type="text" name="driver_name" class="form-control" value="{{ old('driver_name', $request->driver_name) }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>تلفن راننده</label>
                                    <input type="text" name="driver_phone" class="form-control" value="{{ old('driver_phone', $request->driver_phone) }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>نوع وسیله نقلیه</label>
                                    <input type="text" name="vehicle_type" class="form-control" placeholder="مثلاً: وانت، نیسان، کامیون ، تریلی" value="{{ old('vehicle_type', $request->vehicle_type) }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>سری پلاک</label>
                                    <input type="text" name="plate_serial" class="form-control" placeholder="بخش سری (مثلاً: ایران 22)" value="{{ old('plate_serial', $request->plate_serial) }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label> (از راست به چپ)شماره پلاک</label>
                                    <input type="text" name="plate_number" class="form-control" placeholder="شماره پلاک (مثلاً: 123 ب 12)" value="{{ old('plate_number', $request->plate_number) }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>شماره بارنامه</label>
                                    <input type="text" name="bill_of_lading_number" class="form-control" placeholder="شماره بارنامه" value="{{ old('bill_of_lading_number', $request->bill_of_lading_number) }}">
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">تایید و ثبت</button>
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
