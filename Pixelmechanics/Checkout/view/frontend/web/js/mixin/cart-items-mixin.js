define(['jquery'], function($) {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                template: 'Pixelmechanics_Checkout/summary/cart-items'
            }
        });
    }
});
