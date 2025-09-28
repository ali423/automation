{{-- Basic Form Scripts --}}
<script src="{{ asset('js/default-assets/basic-form.js') }}"></script>

{{-- Dynamic Total Value Calculation --}}
<script>
    // Dynamic calculation of total inventory value
    function calculateTotalValue() {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const purchasePrice = parseFloat(document.getElementById('purchase_price').value) || 0;
        const salePriceField = document.querySelector('input[value*="ریال"]:not([name])');

        let totalValue = 0;
        if (salePriceField && salePriceField.value !== 'محاسبه نشده') {
            const salePrice = parseFloat(salePriceField.value.replace(/,/g, '')) || 0;
            totalValue = amount * salePrice;
        } else {
            totalValue = amount * purchasePrice;
        }

        const totalValueField = document.querySelector('input[value*="ارزش کل موجودی"]').parentElement.querySelector('input');
        if (totalValueField) {
            totalValueField.value = totalValue.toLocaleString('fa-IR');
        }
    }

    // Add event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const amountField = document.getElementById('amount');
        const priceField = document.getElementById('purchase_price');

        if (amountField) amountField.addEventListener('input', calculateTotalValue);
        if (priceField) priceField.addEventListener('input', calculateTotalValue);
    });
</script>
