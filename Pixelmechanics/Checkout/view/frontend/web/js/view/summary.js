
define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/step-navigator',
    'uiComponent',
    'Magento_Checkout/js/model/totals',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ($, ko, stepNavigator, Component, totals, messageList) {
    'use strict';

    return Component.extend({
        isLoading: totals.isLoading,

        isVisible: function () {
            return stepNavigator.isProcessed('shipping');
        },
        initialize: function () {
            $(function() {
                $('body').on("click", '#place-order-trigger', function () {

                    var flag = false;
                    $( ".payment-group .payment-method" ).each(function() {
                        if ($(this).hasClass('_active')){
                            flag = true;
                            return false;
                        }
                    });

                    if(flag == true){
                        $(".payment-method._active").find('.action.primary.checkout').trigger( 'click' );
                    }
                    else {
                        var errorMessage = $.mage.__('No Payment Method Selected.')
                        messageList.addErrorMessage({ message: errorMessage });
                        $(window).scrollTop(0);
                    }

                });
            });
            var self = this;
            this._super();
        }
    });
});
