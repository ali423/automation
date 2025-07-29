@extends('layouts.main')
@section('title','ویرایش درخواست تولید کالا')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش درخواست تولید کالا</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('production-request.update', $request) }}" class="needs-validation" novalidate="" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            
                            <!-- Product Selection -->
                            <div class="form-row m-3">
                                <div class="form-group col-md-6">
                                    <label for="product_id">{{ __('fields.commodity.name') }}</label>
                                    <select id="product_id" class="form-control" name="product_id" onchange="loadProductFormula(this)" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ $currentProductId == $product->id ? 'selected' : '' }}>
                                                {{ $product->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        محصول را انتخاب کنید
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="amount">{{ __('fields.commodity.amount') }} (کیلوگرم)</label>
                                    <input type="number" class="form-control" id="amount" name="amount" min="0.001" step="0.001" value="{{ old('amount', $currentAmount) }}" required>
                                    <div class="invalid-feedback">
                                        مقدار تولید را وارد کنید
                                    </div>
                                </div>
                            </div>

                            <!-- Product Formula Display -->
                            <div id="product_formula_section" class="col-lg-12" style="display: none;">
                                <p>فرمول ساخت محصول</p>
                                <div id="formula_display" class="form-row shadow p-4 mb-3">
                                    <!-- Formula will be loaded here by JavaScript -->
                                </div>
                            </div>

                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default" accept="image/*,.pdf,.zip,.rar">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">انتخاب فایل</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-20">
                                <label for="comment">توضیحات</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment" rows="6">{{ old('comment', $request->description) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                                    <a href="{{ route('production-request.index') }}" class="btn btn-secondary">انصراف</a>
                                </div>
                            </div>
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
    <script type="text/javascript">
        // Preload product formulas data
        var productFormulas = {};
        @foreach($products as $product)
            productFormulas[{{ $product->id }}] = [
                @foreach($product->materials as $material)
                    {
                        material_id: {{ $material->id }},
                        material_title: '{{ $material->title }}',
                        amount: {{ $material->pivot->amount }},
                        unit_id: {{ $material->pivot->unit_id }},
                        unit_name: '{{ $material->unit->name }}',
                        unit_symbol: '{{ $material->unit->symbol }}'
                    }@if(!$loop->last),@endif
                @endforeach
            ];
        @endforeach

        // Load product formula when product is selected
        function loadProductFormula(el) {
            var productId = el.value;
            var formulaSection = document.getElementById('product_formula_section');
            var formulaDisplay = document.getElementById('formula_display');
            
            if (productId && productFormulas[productId]) {
                var formula = productFormulas[productId];
                var html = '';
                
                formula.forEach(function(material) {
                    html += '<div class="form-group col-md-4">';
                    html += '<label>نام ماده</label>';
                    html += '<input type="text" value="' + material.material_title + '" class="form-control" disabled>';
                    html += '</div>';
                    html += '<div class="form-group col-md-4">';
                    html += '<label>مقدار مورد نیاز</label>';
                    html += '<input type="text" value="' + material.amount + '" class="form-control" disabled>';
                    html += '</div>';
                    html += '<div class="form-group col-md-4">';
                    html += '<label>واحد</label>';
                    html += '<input type="text" value="' + material.unit_name + ' (' + material.unit_symbol + ')" class="form-control" disabled>';
                    html += '</div>';
                });
                
                formulaDisplay.innerHTML = html;
                formulaSection.style.display = 'block';
            } else {
                formulaSection.style.display = 'none';
            }
        }

        // Load formula on page load if product is already selected
        document.addEventListener('DOMContentLoaded', function() {
            var productSelect = document.getElementById('product_id');
            if (productSelect.value) {
                loadProductFormula(productSelect);
            }
        });
    </script>
@endsection 