{{-- Basic Form Scripts --}}
<script src="{{ asset('js/default-assets/basic-form.js') }}"></script>

{{-- Dynamic Total Value Calculation and Unit Filtering --}}
<script>
    // Preload selectable units data for current commodity
    var commodityUnitsData = {};
    @if(isset($inventory) && $inventory->commodity && isset($selectableUnits))
        commodityUnitsData[{{ $inventory->commodity->id }}] = [
            @foreach($selectableUnits as $unit)
                {
                    id: {{ $unit->id }},
                    name: '{{ $unit->name }}',
                    symbol: '{{ $unit->symbol ?? '' }}',
                    display_name: '{{ $unit->name }}@if($unit->symbol) ({{ $unit->symbol }})@endif'
                }@if(!$loop->last),@endif
            @endforeach
        ];
    @endif

    // Function to load units when commodity changes
    function loadCommodityUnits(commodityId) {
        var $unitSelect = $('#unit_id');
        if (!$unitSelect.length) return;
        
        // Clear unit options first
        $unitSelect.empty().append('<option value="">انتخاب کنید...</option>');
        
        if (!commodityId) {
            return;
        }
        
        // Check if we have preloaded data
        if (commodityUnitsData[commodityId]) {
            var units = commodityUnitsData[commodityId];
            units.forEach(function(unit) {
                $unitSelect.append($('<option></option>')
                    .attr('value', unit.id)
                    .text(unit.display_name));
            });
        } else {
            // Fetch units via AJAX if not preloaded
            $.ajax({
                url: '/order/commodity-units/' + commodityId,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    if (response.success && response.units) {
                        response.units.forEach(function(unit) {
                            var displayName = unit.name + (unit.symbol ? ' (' + unit.symbol + ')' : '');
                            $unitSelect.append($('<option></option>')
                                .attr('value', unit.id)
                                .text(displayName));
                        });
                    }
                },
                error: function() {
                    console.error('خطا در دریافت واحدهای کالا');
                }
            });
        }
    }

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
    $(document).ready(function() {
        $('#amount, #purchase_price').on('input', calculateTotalValue);
        
        // Handle commodity change to update units
        $('#commodity_id').on('change', function() {
            loadCommodityUnits($(this).val());
        });
    });
</script>
