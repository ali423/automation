{{-- Scripts partial for production request forms --}}
<script>
    $(document).ready(function() {
        let currentProductData = null;
        
        // Store original product options for filtering
        const originalOptions = $('#product_id').html();

        // Clear production attribute filters
        $('#clear-production-filters').click(function() {
            $('.production-attribute-filter').prop('checked', false);
            filterProducts();
        });

        // Filter products when attributes change
        $('.production-attribute-filter').change(function() {
            filterProducts();
        });

        function filterProducts() {
            // Get selected attribute IDs
            const selectedAttributes = [];
            $('.production-attribute-filter:checked').each(function() {
                selectedAttributes.push($(this).val());
            });

            // Restore all options first
            $('#product_id').html(originalOptions);

            // If no filters selected, we're done
            if (selectedAttributes.length === 0) {
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
        }

        // Initialize with current values for edit form
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
