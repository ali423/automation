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
                        <form method="post" action="{{ route('commodity.store') }}" class="needs-validation"
                              novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="title"> {{  __('fields.title') }}</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                           id="title" placeholder="عنوان کالا" required="">
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
                                    <label for="sales_price"> {{  __('fields.sales_price') }}</label>
                                    <input type="number" min="100" name="sales_price" value="{{ old('sales_price') }}"
                                           class="form-control"
                                           id="sales_price" placeholder="{{  __('fields.sales_price') }}" required disabled>
                                           <div class="invalid-feedback">حداقل قیمت 100 تومان می باشد</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="purchase_price"> {{  __('fields.purchase_price') }}</label>
                                    <input type="number" min="1" name="purchase_price" value="{{ old('purchase_price') }}"
                                           class="form-control"
                                           id="purchase_price" placeholder="{{  __('fields.purchase_price') }}">
                                </div>
                            </div>

                            <h2>فرمول ساخت برای صد کیلوگرم فراورده</h2>

                            <div class="col-lg-12">
                                <div id="inputFormRow">
                                    <div class="input-group mb-3">
                                            <label for="materials"> {{ __('fields.commodity.material_type') }}</label>
                                            <select id="materials" class="form-control" name="materials[0]" required>
                                                <option value="">انتخاب کنید...</option>
                                              @foreach($materials as $material)
                                                    <option value="{{$material->id}}">{{$material->title}}</option>
                                                @endforeach
                                            </select>
                                        <div class="invalid-feedback"> {{ __('fields.commodity.material_type') }}  را انتخاب کنید</div>
                                        <label for="material_amount"> {{  __('fields.commodity.material_amount') }}</label>
                                        <input type="text" name="material_amount[0]" class="form-control"
                                               id="material_amount" placeholder="{{  __('fields.commodity.material_amount') }}" min="1" max="100" required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{  __('fields.commodity.material_amount') }} را وارد کنید.
                                        </div>                                        <div class="input-group-append">
                                            <button id="removeRow" type="button" class="btn btn-danger">حذف</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-info">اضافه کردن امکانات </button>
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
    <script type="text/javascript">
        // add row
        $("#addRow").click(function () {
            var html = '';
            html += '<div id="inputFormRow">';
            html += '<div class="input-group mb-3">';
            html += '<label for="materials"> {{ __('fields.commodity.material_type') }}</label>';
            html += '   <select id="materials" class="form-control" name="materials[]" required>';
            html += '  <option value="">انتخاب کنید...</option>';
            @foreach($materials as $material)
                html += '<option value="{{$material->id}}">{{$material->title}}</option>';
                   @endforeach
            html += '</select>';

            html += '<div class="invalid-feedback"> {{ __('fields.commodity.material_type') }}  را انتخاب کنید</div>';
            html += ' <label for="material_amount"> {{  __('fields.commodity.material_amount') }}</label>';
            html += ' <input type="text" name="material_amount[]" class="form-control"id="material_amount" placeholder="{{  __('fields.commodity.material_amount') }}" min="1" max="100" required="">';
            html += '  <div class="invalid-feedback">لطفاً {{  __('fields.commodity.material_amount') }} را وارد کنید.</div>';
            html += '<div class="input-group-append">';
            html += '<button id="removeRow" type="button" class="btn btn-danger">حذف</button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

            $('#newRow').append(html);
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).closest('#inputFormRow').remove();
        });
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/commodity.js') }}"></script>
@endsection

