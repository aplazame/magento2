define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'aplazame_payment',
                component: 'Aplazame_Payment/js/view/payment/method-renderer/aplazame-method'
            },
            {
                type: 'aplazame_payment_pay_later',
                component: 'Aplazame_Payment/js/view/payment/method-renderer/aplazame-pay-later-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
