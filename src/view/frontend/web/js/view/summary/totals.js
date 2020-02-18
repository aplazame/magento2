define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function (ko, Component, Quote) {
    'use strict';

    var config = window.checkoutConfig.payment.aplazame_payment;

    return Component.extend({
        cartWidgetIsEnabled: config.cart_widget_enabled,
        instalmentsIsEnabled: config.instalments_enabled,
        cartLegalAdviceIsEnabled: config.cart_legal_advice_enabled,

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
