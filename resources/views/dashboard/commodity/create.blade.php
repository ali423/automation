@extends('layouts.main')
@section('title', 'داشبورد')

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
                                <div class="form-group col-md-6">
                                    <label for="title"> {{ __('fields.title') }}</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                           id="title" placeholder="عنوان کالا" required="">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان کالا را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="type"> {{ __('fields.type') }}</label>
                                    <select id="type" class="form-control" name="type" required>
                                        <option value="">انتخاب کنید...</option>
                                        <option value="material">ماده اولیه</option>
                                        <option value="product">فرآورده</option>
                                    </select>
                                    <div class="invalid-feedback">نوع کالا را انتخاب کنید</div>
                                </div>
                            </div>
                            <div class="form-row">

                                <div class="form-group col-md-6">
                                    <label for="sales_price"> {{ __('fields.warning_limit') }}</label>
                                    <input type="number" step="0.01" name="warning_limit"
                                           value="{{ old('warning_limit') }}"
                                           class="form-control" placeholder="{{ __('fields.warning_limit') }}" required>
                                    <div class="invalid-feedback">{{ __('fields.warning_limit') }} را وارد کنید</div>
                                </div>
                                <div id="sales_price" class="form-group col-md-6">
                                    <label for="sales_price"> {{ __('fields.sales_price') }}</label>
                                    <input type="number" step="0.01" min="100" name="sales_price"
                                           value="{{ old('sales_price') }}"
                                           class="form-control" placeholder="{{ __('fields.sales_price') }}" required>
                                    <div class="invalid-feedback">حداقل قیمت 100 تومان می باشد</div>
                                </div>
                                <div id="purchase_price" class="form-group col-md-6">
                                    <label for="purchase_price"> {{ __('fields.purchase_price') }}</label>
                                    <input type="number" step="0.01" min="100" name="purchase_price"
                                           value="{{ old('purchase_price') }}" class="form-control"
                                           placeholder="{{ __('fields.purchase_price') }}" required>
                                    <div class="invalid-feedback">حداقل قیمت 100 تومان می باشد</div>
                                </div>
                            </div>

                            <div id="product_formul" class="col-lg-12">
                                <p>فرمول ساخت برای صد کیلوگرم فراورده</p>
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    <div class="form-group col-md-5">
                                        <label for="materials"> {{ __('fields.commodity.material_type') }}</label>
                                        <select id="materials" class="form-control" name="materials[0]" required>
                                            <option value="">انتخاب کنید...</option>
                                            @foreach ($materials as $material)
                                                <option value="{{ $material->id }}">{{ $material->title }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ __('fields.commodity.material_type') }} را انتخاب کنید
                                        </div>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label
                                            for="material_amount">{{ __('fields.commodity.material_amount') }}</label>
                                        <input type="number" step="0.01" name="material_amount[0]" class="form-control"
                                               id="material_amount"
                                               placeholder="{{ __('fields.commodity.material_amount') }}" min="1"
                                               max="100" onchange="percentage(this)" required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{ __('fields.commodity.material_amount') }} را وارد کنید
                                        </div>
                                    </div>
                                </div>

                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
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
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"><div class="form-group col-md-5"><label for="materials"> {{ __("fields.commodity.material_type") }}</label><select id="materials" class="form-control" name="materials[1]" required><option value="">انتخاب کنید...</option>@foreach ($materials as $material)<option value="{{ $material->id }}">{{ $material->title }}</option>@endforeach</select><div class="invalid-feedback">{{ __("fields.commodity.material_type") }} را انتخاب کنید</div></div><div class="form-group col-md-5"><label for="material_amount">{{ __("fields.commodity.material_amount") }}</label><input type="number" step="0.01" name="material_amount[0]" class="form-control"id="material_amount"placeholder="{{ __("fields.commodity.material_amount") }}" min="1"max="100" onchange="percentage(this)" required=""><div class="invalid-feedback">لطفاً {{ __("fields.commodity.material_amount") }} را وارد کنید</div></div><div class="form-group col-sm-auto"><label for="" class="d-none d-md-block">&nbsp;</label><button id="removeRow" type="submit" class="btn btn-danger btn-block py-2">حذف</button></div></div>';

            $('#newRow').append(html);

            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('select').setAttribute('name', 'materials[' + index + ']');
                element.querySelector('input').setAttribute('name', 'material_amount[' + index + ']');
            });
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).closest('#inputFormRow').remove();
            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('select').setAttribute('name', 'materials[' + index + ']');
                element.querySelector('input').setAttribute('name', 'material_amount[' + index + ']');
            });
        });
    </script>

    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/commodity.js') }}"></script>
@endsection
