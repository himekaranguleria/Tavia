/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/form',
    'mage/validation/url'
], function ($, form, mageValidationUrl) {
    'use strict';

    return form.extend({

        addMethod: function (method, params) {
            mageValidationUrl.redirect(params.redirectUrl);
        }
    });
});
