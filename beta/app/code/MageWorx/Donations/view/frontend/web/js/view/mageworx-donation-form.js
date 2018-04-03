/*
 global define
 */
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/form',
    'Magento_Checkout/js/model/quote',
    'MageWorx_Donations/js/model/donation',
    'MageWorx_Donations/js/action/apply-donation',
    'MageWorx_Donations/js/action/delete-donation',
    'MageWorx_Donations/js/model/donation-messages',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function ($, ko, Component, quote, donation, applyDonationAction, deleteDonationAction, messageContainer, priceUtils, $t) {
    'use strict';

    var errorMessage = $t('ERROR'),
        isLoading = ko.observable(false);

    return Component.extend({

        defaults: {
            defaultShowButtonAdd: ko.computed(function () {
                if (donation.allData().donation > 0) {
                    return false;
                }
                return true;
            }, this),

            defaultShowButtonDelete: ko.computed(function () {
                if (donation.allData().donation > 0) {
                    return true;
                }
                return false;
            }, this)
        },

        initialize: function () {
            this._super();
            this.showButtonAdd = ko.observable(this.defaultShowButtonAdd);
            this.showButtonDelete = ko.observable(this.defaultShowButtonDelete);

            /* default initialize */
            var valueDonation = donation.allData().donation;
            var formatPrice = priceUtils.formatPrice(valueDonation, quote.getBasePriceFormat());
            this.getValueDonation = ko.observable(formatPrice);
            return this;
        },

        isLoading: isLoading,

        /**
         * Form submit add donation
         *
         */
        onSubmitAdd: function () {
            var self = this;
            this.source.set('params.invalid', false);
            this.source.trigger('mageworxDonationForm.data.validate');

            if (!this.source.get('params.invalid')) {
                isLoading(true);
                var formData = [];
                formData['donation'] = this.source.get('mageworxDonationForm');
                formData['charity'] =  this.source.get('charity');
                var predefinedDonation = this.source.get('predefinedDonation');

                if (formData['donation'] == '' && predefinedDonation != ''){
                    formData['donation'] = predefinedDonation;
                }

                applyDonationAction(formData, isLoading, function (isDonationAddSuccess) {
                    if (isDonationAddSuccess) {
                        var price = priceUtils.formatPrice(formData['donation'], quote.getBasePriceFormat());
                        self.getValueDonation(price);
                        self.showButtonAdd(false);
                        self.showButtonDelete(true);
                    } else {
                        self.showButtonAdd(true);
                        self.showButtonDelete(false);
                    }
                });
            } else {
                messageContainer.addErrorMessage({'message': errorMessage});
            }
        },

        /**
         * Form submit delete donation
         *
         */
        onSubmitDelete: function () {
            this.source.set('params.invalid', false);
            this.source.trigger('mageworxDonationForm.data.validate');

            if (!this.source.get('params.invalid')) {
                isLoading(true);
                var formData = [];
                formData['donation'] = this.source.get('mageworxDonationForm');
                formData['charity'] =  this.source.get('charity');
                deleteDonationAction(formData, isLoading);
                this.getValueDonation(0);
                this.showButtonAdd(true);
                this.showButtonDelete(false);
            } else {
                messageContainer.addErrorMessage({'message': errorMessage});
            }
        },

        /**
         * Is donation use (has in session)
         *
         * @return bool
         */
        isDonationUse: function () {
            return donation.allData().is_donation_use;
        },

        /**
         * Show donation in cart
         *
         * @return bool
         */
        isShowDonationCart: function () {
            return donation.allData().is_show_donation_cart;
        },

        /**
         * Show label with minimum donation
         *
         * @return bool
         */
        isActivateMinimumDonation: function () {
            if (donation.allData().minimum_donation > 0) {
                return true
            }
            return false;

        },

        /**
         * Get minimum donation
         *
         * @return {String}              Formatted value
         */
        getMinimumDonation: function () {
            if (donation.allData().minimum_donation > 0) {
                return priceUtils.formatPrice(donation.allData().minimum_donation, quote.getBasePriceFormat())
            }
        },

        /**
         * Get default donation
         *
         * @return {String}
         */
        getDefaultDonation: function () {
            return donation.allData().default_description_donation;
        },

        /**
         * Show title donation
         *
         * @returns bool
         */
        isDisplayTitle: function () {
            return donation.allData().is_display_title;
        },

        /**
         * Show block default donation
         *
         * @returns bool
         */
        isDisplayBlockDefaultDonation: function () {
            if (donation.allData().default_description_donation != null) {
                return true;
            }
            return false;
        },

        isDisplayed: function () {
            return donation.allData().is_enable;
        }
    });
});