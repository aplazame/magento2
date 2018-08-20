define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/place-order'
    ],
    function (
        $,
        Component,
        redirectOnSuccessAction,
        setPaymentInformationAction,
        additionalValidators,
        placeOrderService
    ) {
        'use strict';

        var config = window.checkoutConfig.payment.aplazame_payment;

        return Component.extend({
            defaults: {
              template: 'Aplazame_Payment/payment/form'
            },

            redirectAfterPlaceOrder: false,

            /**
             * @override
             */
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);

                    $.when(
                        setPaymentInformationAction(
                            this.messageContainer,
                            {
                                method: this.getCode()
                            }
                        )
                    )
                    .then(this.createAplazameCheckout.bind(this))
                    .done(this.launchAplazameCheckout.bind(this))
                    .fail(
                        function () {
                            self.isPlaceOrderActionAllowed(true);
                        }
                    );
                }
            },

            createAplazameCheckout: function () {
                return placeOrderService('/aplazame/payment/index', {}, this.messageContainer);
            },

            launchAplazameCheckout: function (payload) {
                aplazame.checkout(
                    payload.id,
                    {
                        onDismiss: function () {
                            this.isPlaceOrderActionAllowed(true);
                        }.bind(this),
                        onKO: function () {
                            this.isPlaceOrderActionAllowed(true);
                        }.bind(this),
                        onError: function () {
                            this.isPlaceOrderActionAllowed(true);
                        }.bind(this),
                        onPending: function () {
                            this.success();
                        }.bind(this),
                        onSuccess: function () {
                            this.success();
                        }.bind(this)
                    }
                );
            },

            success: function() {
                redirectOnSuccessAction.redirectUrl = '/aplazame/payment/success';
                redirectOnSuccessAction.execute();
            },

            button: function () {
                aplazame.button({
                    selector: config.button.selector,
                    amount: config.button.amount,
                    currency: config.button.currency
                });
            },
        });
    }
);
