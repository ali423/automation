@extends('layouts.main')
@section('title', 'ویرایش کالا')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش کالا</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('commodity.update', $commodity) }}"
                              class="needs-validation"
                              novalidate="">
                            @method('PATCH')
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="title"> {{ __('fields.title') }}</label>
                                    <input type="text" name="title" value="{{ $commodity->title }}" class="form-control"
                                           id="title" placeholder="عنوان کالا" required="">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان کالا را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="type"> {{ __('fields.type') }}</label>
                                    <select id="type" class="form-control" name="type" disabled>
                                        @if ($commodity->type == 'material')
                                            <option value="material" selected>ماده اولیه</option>
                                            <option value="product">فرآورده</option>
                                        @else
                                            <option value="product" selected>فرآورده</option>
                                            <option value="material">ماده اولیه</option>
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">نوع کالا را انتخاب کنید</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="sales_price"> {{ __('fields.warning_limit') }}</label>
                                    <input type="number" step="0.01" name="warning_limit"
                                           value="{{ $commodity->warning_limit }}"
                                           class="form-control" placeholder="{{ __('fields.warning_limit') }}" required>
                                    <div class="invalid-feedback">{{ __('fields.warning_limit') }} را وارد کنید</div>
                                </div>
                            @if(!empty($commodity->sales_price))
                                    <div id="sales_price" class="form-group col-md-6">
                                        <label for="sales_price"> {{ __('fields.sales_price') }}</label>
                                        <input type="number" step="0.01" min="100" name="sales_price"
                                               value="{{ $commodity->sales_price }}"
                                               class="form-control" placeholder="{{ __('fields.sales_price') }}"
                                               required>
                                        <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
                                    </div>
                                @elseif(!empty($commodity->purchase_price))
                                    <div id="purchase_price" class="form-group col-md-6">
                                        <label for="purchase_price"> {{ __('fields.purchase_price') }}</label>
                                        <input type="number" step="0.01" min="100" name="purchase_price"
                                               value="{{ $commodity->purchase_price }}" class="form-control"
                                               placeholder="{{ __('fields.purchase_price') }}" required>
                                        <div class="invalid-feedback">حداقل قیمت 100 ریال می باشد</div>
                                    </div>
                                @endif
                            </div>

                            @if ($commodity->type == 'product')
                                <div id="product_formul" class="col-lg-12">
                                    <p>فرمول ساخت برای صد کیلوگرم فراورده</p>
                                    @foreach($used_materials as $used_material)
                                        <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                            <div class="form-group col-md-5"><label
                                                    for="materials"> {{ __("fields.commodity.material_type") }}</label>
                                                <select id="materials" class="form-control" name="materials[]"
                                                        required>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}"
                                                                @if($material->id == $used_material->id )
                                                                selected
                                                            @endif
                                                        >{{ $material->title }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">{{ __("fields.commodity.material_type") }}
                                                    را
                                                    انتخاب کنید
                                                </div>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <label
                                                    for="material_amount">{{ __("fields.commodity.material_amount") }}</label>
                                                <input type="number" step="0.01" name="material_amount[]"
                                                       class="form-control"
                                                       id="material_amount"
                                                       value="{{ $used_material->pivot->percentage }}"
                                                       placeholder="{{ __("fields.commodity.material_amount") }}"
                                                       max="100" onchange="percentage(this)" required="">
                                                <div class="invalid-feedback">
                                                    لطفاً {{ __("fields.commodity.material_amount") }}
                                                    را وارد کنید
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-auto"><label for=""
                                                                                       class="d-none d-md-block">&nbsp;</label>
                                                <button id="removeRow" type="submit"
                                                        class="btn btn-danger btn-block py-2">
                                                    حذف
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div id="newRow"></div>
                                    <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                                </div>
                            @endif
                            <button type="submit" class="btn btn-primary mr-2">ویرایش</button>
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
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"><div class="form-group col-md-5"><label for="materials"> {{ __("fields.commodity.material_type") }}</label><select id="materials" class="form-control" name="materials[1]" required><option value="">انتخاب کنید...</option>@foreach ($materials as $material)<option value="{{ $material->id }}">{{ $material->title }}</option>@endforeach</select><div class="invalid-feedback">{{ __("fields.commodity.material_type") }} را انتخاب کنید</div></div><div class="form-group col-md-5"><label for="material_amount">{{ __("fields.commodity.material_amount") }}</label><input type="number" step="0.01" name="material_amount[0]" class="form-control"id="material_amount"placeholder="{{ __("fields.commodity.material_amount") }}" max="100" onchange="percentage(this)" required=""><div class="invalid-feedback">لطفاً {{ __("fields.commodity.material_amount") }} را وارد کنید</div></div><div class="form-group col-sm-auto"><label for="" class="d-none d-md-block">&nbsp;</label><button id="removeRow" type="submit" class="btn btn-danger btn-block py-2">حذف</button></div></div>';

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
