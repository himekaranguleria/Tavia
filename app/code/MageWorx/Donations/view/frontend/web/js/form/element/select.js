define([
    'ko',
    'Magento_Ui/js/form/element/select'
], function (ko, Select) {
    'use strict';

    var hasImage = ko.observable(false);

    return Select.extend({

        hasImage: hasImage,

        getCharityDescription: function () {
            var value = this.value(),
                uiElements = require('uiRegistry').get('component = MageWorx_Donations/js/form/element/select').options();

            for (var index = 0; index < uiElements.length; ++index) {
                if (uiElements[index]['value'] == value){
                    return uiElements[index]['notice'];
                }
            }
        },

        imagePath: function(){
            var value = this.value(),
                uiElements = require('uiRegistry').get('component = MageWorx_Donations/js/form/element/select').options();

            hasImage(false);
            for (var index = 0; index < uiElements.length; ++index) {
                if (uiElements[index]['value'] == value){
                    hasImage(true);
                    return uiElements[index]['path'];
                }
            }
        }
    });
});
