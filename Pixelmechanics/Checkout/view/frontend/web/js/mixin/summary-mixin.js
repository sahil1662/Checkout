define([
    'jquery',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ($, stepNavigator, messageList) {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                template: 'Pixelmechanics_Checkout/summary'
            },
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
    }
});