define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function (ko, Component, Quote) {
    'use strict';

    return Component.extend({
        getAmount: function () {
            return Quote.totals().base_grand_total;
        },

        getCurrency: function () {
            return Quote.totals().quote_currency_code;
        },

        toAplazameDecimal: function (number) {
            return parseInt(number * 100)
        },
    });
});
