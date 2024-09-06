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
        cartDownpaymentInfoIsEnabled: config.cart_downpayment_info_enabled,
        cartPayIn4IsEnabled: config.cart_pay_in_4_enabled,
        cartDefaultInstalments: config.cart_default_instalments,
        widgetOutOfLimits: config.widget_out_of_limits,
        cartWidgetVer: config.cart_widget_ver,
        cartMaxDesired: config.cart_max_desired_enabled,
        cartPrimaryColor: config.cart_widget_primary_color,
        cartLayout: config.cart_widget_layout,
        cartAlign: config.cart_widget_align,
        cartSliderIsEnabled: config.cart_slider_enabled,
        cartSmallSizeIsEnabled: config.cart_small_size_enabled,

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
