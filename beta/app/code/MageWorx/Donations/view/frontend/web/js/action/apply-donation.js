/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'jquery',
        'MageWorx_Donations/js/model/donation',
        'MageWorx_Donations/js/model/donation-messages',
        'mage/translate',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals'
    ],
    function ($,
              donation,
              messageContainer,
              $t,
              getPaymentInformationAction,
              totals) {
        'use strict';
        return function (formData, isLoading, isDonationAddSuccess) {
            var data = {'donation': formData['donation'], 'charity':formData['charity']},
                successMessage = $t('donation was successfully applied.'),
                errorMessage = $t('Could not apply donation');

            $.ajax({
                    url: donation.allData().url,
                    data: data,
                    type: 'post',
                    dataType: 'json'
                })
                .done(function (response) {
                    if (response) {
                        if (response['result'] == 'false') {
                            isDonationAddSuccess(false);
                            isLoading(false);
                            totals.isLoading(false);
                        } else {
                            var deferred = $.Deferred();
                            isLoading(false);
                            isDonationAddSuccess(true);
                            totals.isLoading(true);
                            getPaymentInformationAction(deferred);
                            $.when(deferred).done(function () {
                                totals.isLoading(false);
                            });
                            messageContainer.addSuccessMessage({'message': successMessage});
                        }
                    } else {
                        isLoading(false);
                        totals.isLoading(false);
                        messageContainer.addErrorMessage({'message': successMessage});
                    }
                })
                .fail(
                    function (response) {
                        isLoading(false);
                        totals.isLoading(false);
                        messageContainer.addErrorMessage({'message': errorMessage});
                    }
            );
        };
    }
);