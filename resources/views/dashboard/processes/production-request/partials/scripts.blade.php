{{-- Scripts partial for production request forms --}}
<script>
    $(document).ready(function() {
        let currentProductData = null;
        
        // Store original product options for filtering
        const originalOptions = $('#product_id').html();

        // Clear production attribute filters
        $('#clear-production-filters').click(function() {
            $('.production-attribute-filter').removeClass('btn-primary').addClass('btn-outline-primary');
            filterProducts();
            updateFilterIndicator();
        });

        // Filter products when attribute button clicked
        $(document).on('click', '.production-attribute-filter', function() {
            const $btn = $(this);
            if ($btn.hasClass('btn-primary')) {
                $btn.removeClass('btn-primary').addClass('btn-outline-primary');
            } else {
                $btn.removeClass('btn-outline-primary').addClass('btn-primary');
            }
            filterProducts();
            updateFilterIndicator();
        });

        // Search in attributes
        $('#production-attribute-search').on('input', function() {
            const q = $(this).val().toString().trim().toLowerCase();
            $('.production-attribute-filter').each(function() {
                const name = $(this).data('name').toString().toLowerCase();
                $(this).toggle(name.includes(q));
            });
        });

        // Update filter indicator (icon color)
        function updateFilterIndicator() {
            const count = $('.production-attribute-filter.btn-primary').length;
            const $icon = $('.attribute-filter-container .toggle-row-filter');
            if (count > 0) {
                $icon.css('color', '#007bff');
            } else {
                $icon.css('color', '#666');
            }
        }

        function getPiecesPerBoxFromSelection() {
            const $opt = $('#product_id option:selected');
            const v = $opt.data('pieces-per-box');
            const n = Number(v);
            return (n && n > 0) ? n : null;
        }

        function updatePackagingFieldsVisibility() {
            const ppb = getPiecesPerBoxFromSelection();
            if (ppb) {
                $('#pieces_per_box_display').val(String(ppb));
                $('#packaging_count_group').show();
                $('#pieces_per_box_group').show();
            } else {
                $('#packaging_count').val('');
                $('#pieces_per_box_display').val('-');
                $('#packaging_count_group').hide();
                $('#pieces_per_box_group').hide();
            }
        }

        function syncAmountFromPackaging() {
            const ppb = getPiecesPerBoxFromSelection();
            if (!ppb) {
                return;
            }
            const pc = parseInt($('#packaging_count').val(), 10);
            if (!pc || pc <= 0) {
                return;
            }
            const newAmount = pc * ppb;
            $('#amount').val(String(newAmount));
            const amount = parseFloat($('#amount').val()) || 0;
            if (currentProductData && amount > 0) {
                updateMaterialsDisplay(amount);
            }
        }

        function syncPackagingFromAmount() {
            const ppb = getPiecesPerBoxFromSelection();
            if (!ppb) {
                return;
            }
            const amountVal = parseFloat($('#amount').val());
            if (!amountVal || amountVal <= 0) {
                return;
            }
            const packagingVal = Math.floor(amountVal / ppb);
            if (packagingVal > 0) {
                $('#packaging_count').val(String(packagingVal));
            }
        }

        function filterProducts() {
            // Get selected attribute IDs
            const selectedAttributes = [];
            $('.production-attribute-filter.btn-primary').each(function() {
                selectedAttributes.push($(this).data('attribute-id').toString());
            });

            // Restore all options first
            $('#product_id').html(originalOptions);

            // If no filters selected, we're done
            if (selectedAttributes.length === 0) {
                updatePackagingFieldsVisibility();
                return;
            }

            // Filter options based on attributes
            const $productSelect = $('#product_id');
            $productSelect.find('option').each(function() {
                const $option = $(this);
                const optionValue = $option.val();
                
                // Skip the default "انتخاب کنید" option
                if (!optionValue) {
                    return;
                }

                const productAttributes = ($option.data('attributes') || '').toString().split(',').filter(Boolean);
                
                // Check if product has ALL selected attributes (AND logic)
                const hasAllAttributes = selectedAttributes.every(attrId => 
                    productAttributes.includes(attrId.toString())
                );

                // Hide option if it doesn't match
                if (!hasAllAttributes) {
                    $option.remove();
                }
            });
            updatePackagingFieldsVisibility();
        }

        // Initialize with current values for edit form
        const currentProductId = $('#product_id').val();
        const currentAmount = parseFloat($('#amount').val()) || 0;
        
        if (currentProductId) {
            const unitDisplay = $('#product_id').find('option:selected').data('unit');
            $('#unit-display').text(unitDisplay ? `(${unitDisplay})` : '');
            updatePackagingFieldsVisibility();
            if (getPiecesPerBoxFromSelection() && !$('#packaging_count').val()) {
                syncPackagingFromAmount();
            }
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
            updatePackagingFieldsVisibility();
            if (productId && getPiecesPerBoxFromSelection()) {
                syncPackagingFromAmount();
            } else {
                $('#packaging_count').val('');
            }
            
            if (productId) {
                loadProductMaterials(productId);
            } else {
                hideMaterials();
            }
        });

        $('#amount').on('input', function() {
            syncPackagingFromAmount();
            const amount = parseFloat($(this).val()) || 0;
            
            if (currentProductData && amount > 0) {
                updateMaterialsDisplay(amount);
            }
        });

        $(document).on('input', '#packaging_count', function() {
            syncAmountFromPackaging();
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
