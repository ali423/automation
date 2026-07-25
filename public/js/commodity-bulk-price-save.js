/**
 * Submit only changed rows from bulk commodity price forms.
 * Expects data-original-* attributes on editable inputs.
 */
(function (window, $) {
    'use strict';

    function normalizeNumber(value) {
        if (value === null || value === undefined) {
            return '';
        }
        var trimmed = String(value).trim();
        if (trimmed === '') {
            return '';
        }
        var num = Number(trimmed);
        return Number.isNaN(num) ? trimmed : String(num);
    }

    function isProductRowDirty($row) {
        var $sales = $row.find('.sales-price-input');
        var $discount = $row.find('.discount-input');
        if (!$sales.length) {
            return false;
        }

        var salesDirty = normalizeNumber($sales.val()) !== normalizeNumber($sales.data('original'));
        var discountDirty = normalizeNumber($discount.val()) !== normalizeNumber($discount.data('original'));
        return salesDirty || discountDirty;
    }

    function isMaterialRowDirty($row) {
        var $purchase = $row.find('.purchase-price-input');
        if (!$purchase.length) {
            return false;
        }

        return normalizeNumber($purchase.val()) !== normalizeNumber($purchase.data('original'));
    }

    function bindDirtyOnlyPriceForm(formSelector, mode) {
        var $form = $(formSelector);
        if (!$form.length) {
            return;
        }

        $form.on('submit', function (e) {
            var dirtyCount = 0;
            var isDirtyFn = mode === 'material' ? isMaterialRowDirty : isProductRowDirty;

            $form.find('tr[data-id]').each(function () {
                var $row = $(this);
                var dirty = isDirtyFn($row);
                $row.find('input[name^="prices"]').prop('disabled', !dirty);
                if (dirty) {
                    dirtyCount += 1;
                }
            });

            if (dirtyCount === 0) {
                e.preventDefault();
                $form.find('input[name^="prices"]').prop('disabled', false);
                alert('هیچ تغییری برای ذخیره وجود ندارد.');
            }
        });
    }

    window.bindDirtyOnlyPriceForm = bindDirtyOnlyPriceForm;
})(window, jQuery);
