/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
         'jquery',
        'Magento_Payment/js/view/payment/cc-form',

    'Magento_Checkout/js/action/place-order',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/payment/additional-validators',
    'mage/url',
    'Magento_Checkout/js/view/payment/default',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/quote'

    ],
    function (
        $,
        Component,
        placeOrderAction,
        selectPaymentMethodAction,
        customer,
        checkoutData,
        additionalValidators,
        url
    ) {
        'use strict';

        return Component.extend(
            {
                defaults: {
                    template: 'Modulebazaar_Firstdataapi/payment/firstdataapi'
                },

                /**
                 * Returns send check to info
                 */
                getMailingAddress: function () {
                    return window.checkoutConfig.payment.checkmo.mailingAddress;
                },

                context: function () {
                    return this;
                },

                isShowLegend: function () {
                    return true;
                },


                getCode: function () {
                    return 'firstdataapi';
                },

                isActive: function () {
                    return true;
                },

                validate: function () {
                    var $form = $('#' + this.getCode() + '-form');
                    return $form.validation() && $form.validation('isValid');
                },

                placeOrder: function (data, event) {
                    if (event) {
                        event.preventDefault();
                    }
                    var self = this,
                    placeOrder,
                    emailValidationResult = customer.isLoggedIn(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]';
                    if (!customer.isLoggedIn()) {
                        $(loginFormSelector).validation();
                        emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                    }
                    if (emailValidationResult && this.validate() && additionalValidators.validate()) {
                        this.isPlaceOrderActionAllowed(false);
                        placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                        $.when(placeOrder).fail(
                            function () {
                                self.isPlaceOrderActionAllowed(true);
                            }
                        ).done(this.afterPlaceOrder.bind(this));
                        return true;
                    }
                    return false;
                },

                selectPaymentMethod: function () {
                    selectPaymentMethodAction(this.getData());
                    checkoutData.setSelectedPaymentMethod(this.item.method);
                    return true;
                },

                afterPlaceOrder: function () {
                    window.location.replace(url.build('firstdataapi/index/index/'));
                },
           
            }
        );
    }
);
