var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Pixelmechanics_Checkout/js/mixin/shipping-mixin': true
            },
            'Amazon_Payment/js/view/shipping': {
                'Pixelmechanics_Checkout/js/mixin/shipping-amazon-mixin': true
            },
            'Magento_Checkout/js/view/summary/item/details': {
                'Pixelmechanics_Checkout/js/mixin/details-mixin': true
            },
            'Magento_Checkout/js/view/summary/cart-items': {
                'Pixelmechanics_Checkout/js/mixin/cart-items-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'Pixelmechanics_Checkout/js/mixin/shipping-information-mixin': true
            },
            'Magento_Checkout/js/view/form/element/email': {
                'Pixelmechanics_Checkout/js/mixin/email-mixin': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'Pixelmechanics_Checkout/js/mixin/billing-address-mixin': true
            },
            'Magento_Checkout/js/view/summary': {
                'Pixelmechanics_Checkout/js/mixin/summary-mixin': true
            }
        }
    }
};
