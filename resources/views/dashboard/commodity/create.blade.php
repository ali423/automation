@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">کالا جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('commodity.store') }}" class="needs-validation" novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="title"> {{  __('fields.title') }}</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                           id="title" placeholder="عنوان کالا"  required="">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان کالا را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="type"> {{ __('fields.type') }}</label>
                                    <select id="type" class="form-control" name="type" required>
                                        <option value="">انتخاب کنید...</option>
                                        <option value="material">ماده اولیه</option>
                                        <option value="product">فرآورده</option>
                                    </select>
                                    <div class="invalid-feedback">نوع کالا را انتخاب کنید</div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="amount"> {{  __('fields.amount') }}</label>
                                    <input type="number" min="1" name="amount" value="{{ old('amount') }}" class="form-control"
                                           id="amount" placeholder="قیمت فروش فراورده (تومان)" >
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ثبت کالا</button>
                            <a href="{{ route('commodity.index') }}" class="btn btn-danger">انصراف</a>
                        </form>

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

