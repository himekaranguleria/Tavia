/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';
        var tempAllDonationData = window.mageworxDonationData,
            allData = ko.observable(tempAllDonationData);

        return {
            allData: allData,

            getData: function () {
                return allData;
            },

            setData: function (data) {
                allData(data);
            },

            validate: function () {
                if (this.allData().is_enable) {
                    return this.allData().is_valid;
                }
                return true;
            }
        }
    }
);
