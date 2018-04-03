/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'ko',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (ko, Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({

            defaults: {
                template: 'MageWorx_Donations/summary/donation'
            },

            totals: quote.getTotals(),

            isDisplayed: function () {
                if (this.isFullMode()) {
                    var price = 0;
                    if (this.totals() && totals.getSegment('mageworx_donation')) {
                        price = totals.getSegment('mageworx_donation').value;
                        if (price > 0) {
                            return true;
                        }
                    }
                    return false;
                }
                return false;
            },

            getValue: function () {
                var price = 0;
                if (this.totals() && totals.getSegment('mageworx_donation')) {
                    price = totals.getSegment('mageworx_donation').value;
                }
                return this.getFormattedPrice(price);
            },

            getBaseValue: function () {
                var price = 0;
                if (this.totals()) {
                    price = this.totals().base_donation;
                }
                return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            },

            formatPrice: function (price) {
                return this.getFormattedPrice(price);
            },

            getDetails: function () {
                var donationSegment = totals.getSegment('mageworx_donation');
                if (donationSegment && donationSegment.extension_attributes) {
                    return donationSegment.extension_attributes.mageworx_donations_details;
                }
                return [];
            }
        });
    }
);