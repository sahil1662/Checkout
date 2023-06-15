define([
    'jquery',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar'
], function($, stepNavigator, sidebarModel) {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                template: 'Pixelmechanics_Checkout/shipping-information'
            },

            /**
             * Back step.
             */
            back: function () {
                sidebarModel.hide();
                stepNavigator.navigateTo('shipping');
                $('html, body').animate({scrollTop: '+= -150px'}, 800);
            },

            /**
             * Back to shipping method.
             */
            backToShippingMethod: function () {
                sidebarModel.hide();
                stepNavigator.navigateTo('shipping', 'opc-shipping_method');
                $('html, body').animate({scrollTop: '+=300px'}, 800);
            }
        });
    }
});
