@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">انبار جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('warehouse.store') }}" class="needs-validation" novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="title"> {{  __('fields.title') }}</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                           id="title" placeholder="عنوان انبار"  required="">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان انبار را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="capacity"> {{  __('fields.capacity') }}</label>
                                    <input type="number" min="1" name="capacity" value="{{ old('capacity') }}" class="form-control"
                                           id="capacity" autocomplete="off" placeholder="{{  __('fields.capacity') }}"  required="">
                                    <div class="invalid-feedback">
                                        لطفاً ظرفیت انبار را وارد کنید.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="type"> {{ __('fields.type') }}</label>
                                    <select id="type" class="form-control" name="type" required>
                                        <option value="">انتخاب کنید...</option>
                                        @foreach( __('fields.warehouse.types') as $key=>$value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">نوع انبار را انتخاب کنید</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status"> {{ __('fields.status') }}</label>
                                    <select id="status" class="form-control" name="status" required>
                                        @foreach( __('fields.warehouse.status') as $key=>$value)
                                            <option value="{{$key}}"
                                            @if($key == 'active')
                                                selected
                                            @endif
                                            >{{$value}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">وضعیت انبار را انتخاب کنید</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ثبت انبار</button>
                            <a href="{{ route('warehouse.index') }}" class="btn btn-danger">انصراف</a>
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

