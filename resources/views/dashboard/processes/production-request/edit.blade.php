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
                            
                            <div class="form-row m-3">
                                <div class="form-group col-md-6">
                                    <label for="product_id">{{ __('fields.production-request.product_id') }}</label>
                                    <select id="product_id" class="form-control" name="product_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    {{ $request->product_id == $product->id ? 'selected' : '' }}
                                                    data-materials="{{ $product->materials->count() }}"
                                                    data-unit="{{ $product->unit ? $product->unit->symbol : '' }}">
                                                {{ $product->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        محصول را انتخاب کنید
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="amount">{{ __('fields.production-request.production_amount') }} <span id="unit-display"></span></label>
                                    <input type="number" class="form-control" id="amount" name="amount" min="0.001" step="0.001" value="{{ old('amount', $request->production_amount) }}" required>
                                    <div class="invalid-feedback">
                                        مقدار تولید را وارد کنید
                                    </div>
                                </div>
                            </div>

                            <!-- Required Materials Section -->
                            <div id="materials-section" class="m-3" style="display: none;">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">مواد اولیه مورد نیاز</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="materials-list">
                                            <!-- Materials will be loaded here dynamically -->
                                        </div>
                                        <div id="materials-summary" class="mt-3">
                                            <!-- Summary will be shown here -->
                                        </div>
                                    </div>
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
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script>
        $(document).ready(function() {
            let currentProductData = null;

            // Initialize with current values
            const currentProductId = $('#product_id').val();
            const currentAmount = parseFloat($('#amount').val()) || 0;
            
            if (currentProductId) {
                const unitDisplay = $('#product_id').find('option:selected').data('unit');
                $('#unit-display').text(unitDisplay ? `(${unitDisplay})` : '');
                loadProductMaterials(currentProductId);
                if (currentAmount > 0) {
                    // Wait for data to load, then update display
                    setTimeout(() => {
                        updateMaterialsDisplay(currentAmount);
                    }, 100);
                }
            }

            $('#product_id').change(function() {
                const productId = $(this).val();
                const unitDisplay = $(this).find('option:selected').data('unit');
                
                $('#unit-display').text(unitDisplay ? `(${unitDisplay})` : '');
                
                if (productId) {
                    loadProductMaterials(productId);
                } else {
                    hideMaterials();
                }
            });

            $('#amount').on('input', function() {
                const amount = parseFloat($(this).val()) || 0;
                
                if (currentProductData && amount > 0) {
                    updateMaterialsDisplay(amount);
                }
            });

            function loadProductMaterials(productId) {
                // Show loading state
                showMaterials();
                $('#materials-list').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">در حال بارگذاری...</span>
                        </div>
                        <p class="mt-2">در حال بارگذاری مواد اولیه...</p>
                    </div>
                `);
                
                // Load product data via AJAX
                $.ajax({
                    url: '{{ route("api.production.inventory") }}',
                    method: 'GET',
                    data: { product_id: productId },
                    success: function(response) {
                        currentProductData = response;
                        updateMaterialsDisplay(parseFloat($('#amount').val()) || 0);
                    },
                    error: function(xhr) {
                        $('#materials-list').html(`
                            <div class="alert alert-danger">
                                <strong>خطا:</strong> در بارگذاری مواد اولیه مشکلی پیش آمده است.
                            </div>
                        `);
                    }
                });
            }

            function updateMaterialsDisplay(amount) {
                if (!currentProductData || !currentProductData.materials) {
                    return;
                }

                let materialsHtml = '';

                currentProductData.materials.forEach(function(material) {
                    const requiredAmount = material.amount * amount;
                    
                    materialsHtml += `
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>${material.title}</strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted">مقدار مورد نیاز:</span>
                                <span class="font-weight-bold">${requiredAmount.toFixed(4)} ${material.unit_symbol}</span>
                            </div>
                        </div>
                    `;
                });

                $('#materials-list').html(materialsHtml);
                
                // Show summary
                $('#materials-summary').html(`
                    <div class="alert alert-info">
                        <strong>خلاصه:</strong> برای تولید ${amount} ${currentProductData.product.unit} از محصول "${currentProductData.product.title}"، 
                        ${currentProductData.materials.length} ماده اولیه مورد نیاز است.
                    </div>
                `);
            }

            function showMaterials() {
                $('#materials-section').show();
            }

            function hideMaterials() {
                $('#materials-section').hide();
                currentProductData = null;
            }

            function showNoMaterials() {
                $('#materials-section').show();
                $('#materials-list').html(`
                    <div class="alert alert-warning">
                        <strong>هشدار:</strong> این محصول فرمول ساخت ندارد. 
                        ابتدا فرمول ساخت محصول را در بخش کالاها تعریف کنید.
                    </div>
                `);
                $('#materials-summary').html('');
            }
        });
    </script>
@endsection 