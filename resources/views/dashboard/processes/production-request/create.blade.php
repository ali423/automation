@extends('layouts.main')
@section('title','درخواست تولید کالا')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ثبت درخواست تولید کالا</h4>

                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('production-request.store') }}" class="needs-validation forms-sample" enctype="multipart/form-data" novalidate="">
                            @csrf
                            
                            <!-- Product Selection -->
                            <div class="form-row m-3">
                                <div class="form-group col-md-6">
                                    <label for="product_id">{{ __('fields.commodity.name') }}</label>
                                    <select id="product_id" class="form-control" name="product_id" onchange="loadProductFormula(this)" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->title }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        محصول را انتخاب کنید
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="amount">{{ __('fields.commodity.amount') }} (کیلوگرم)</label>
                                    <input type="number" class="form-control" id="amount" name="amount" min="0.001" step="0.001" required>
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
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment" rows="6">{{ is_array(old('comment')) ? '' : old('comment') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">ثبت درخواست</button>
                            <a href="{{ route('production-request.index') }}" class="btn btn-danger">انصراف</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
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
            
            if (!productId) {
                formulaSection.style.display = 'none';
                return;
            }
            
            var formula = productFormulas[productId];
            if (!formula || formula.length === 0) {
                formulaSection.style.display = 'none';
                return;
            }
            
            // Build formula display HTML
            var html = '';
            formula.forEach(function(material, index) {
                html += '<div class="form-group col-md-4">';
                html += '<label>ماده اولیه ' + (index + 1) + '</label>';
                html += '<input type="text" value="' + material.material_title + '" class="form-control" readonly>';
                html += '</div>';
                html += '<div class="form-group col-md-4">';
                html += '<label>مقدار مورد نیاز (برای ۱۸۵ کیلوگرم)</label>';
                html += '<input type="text" value="' + material.amount + ' ' + material.unit_name + ' (' + material.unit_symbol + ')" class="form-control" readonly>';
                html += '</div>';
                html += '<div class="form-group col-md-4">';
                html += '<label>واحد</label>';
                html += '<input type="text" value="' + material.unit_name + ' (' + material.unit_symbol + ')" class="form-control" readonly>';
                html += '</div>';
            });
            
            formulaDisplay.innerHTML = html;
            formulaSection.style.display = 'block';
        }
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/active.js') }}"></script>
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection 