/**
 * Created by styop on 08.02.2017.
 */
/*global alert*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'mage/translate'
    ],
    function (
        Component,
        quote,
        priceUtils,
        totals,
        $t
    ) {
        "use strict";
        return Component.extend({
            defaults: {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'Netzexpert_MinimumOrderFee/checkout/summary/minimumorderfee'
            },
            totals: quote.getTotals(),
            isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,
            isDisplayed: function() {
                return (this.isFullMode() && this.getValue() > 0);
            },
            getValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('minimumorderfee').value;
                }
                return price;
            },
            getFormattedValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('minimumorderfee').value;
                }
                return this.getFormattedPrice(price);
            },
            getBaseValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = this.totals().base_minimumorderfee;
                }
                return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            },
            getDetails: function() {
                return $t('(Up to an order value of %1)').replace('%1', checkoutConfig.minimumOrderAmount);
            }
        });
    }
);