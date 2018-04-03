/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'ko',
    'Magento_Ui/js/lib/core/collection',
    'jquery',
    'jquery/ui'
], function (ko, uiColumns, jQuery) {
    'use strict';

    jQuery(document).ready(function() {
        jQuery('body').on('change', '[name="predefinedDonation"] select.select', function() {
            var self = jQuery(this);
            var donationForm = jQuery('[name="mageworxDonationForm"]');
            var inputDonationForm = jQuery('[name="mageworxDonationForm"] input');

            if (self.val() == "custom_donation") {
                inputDonationForm.val('');
                donationForm.show();
            } else {
                donationForm.hide();
                inputDonationForm.val(self.val());
            }
        });
    });

    return uiColumns.extend({});
});