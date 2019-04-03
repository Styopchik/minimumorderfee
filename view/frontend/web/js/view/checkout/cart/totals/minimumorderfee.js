/**
 * Created by styop on 08.02.2017.
 */
define(
    [
        'Netzexpert_MinimumOrderFee/js/view/checkout/summary/minimumorderfee'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            /**
             * @override
             * use to define amount is display setting
             */
            isDisplayed: function () {
                return this.getValue() > 0;
            }
        });
    }
);