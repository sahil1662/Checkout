define([
    'jquery',
    'Magento_Customer/js/action/check-email-availability',
    'Magento_Customer/js/action/login',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/validation'
], function($, checkEmailAvailability, loginAction, checkoutData, fullScreenLoader) {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                template: 'Pixelmechanics_Checkout/form/element/email',
                email: checkoutData.getInputFieldEmailValue(),
                emailFocused: false,
                isLoading: false,
                isPasswordVisible: false,
                listens: {
                    email: 'emailHasChanged',
                    emailFocused: 'validateEmail'
                },
                ignoreTmpls: {
                    email: true
                }
            },

            /**
             * Check email existing.
             */
            checkEmailAvailability: function () {
                this.validateRequest();
                this.isEmailCheckComplete = $.Deferred();
                this.isLoading(true);
                this.checkRequest = checkEmailAvailability(this.isEmailCheckComplete, this.email());

                $.when(this.isEmailCheckComplete).done(function () {
                    this.isPasswordVisible(false);
                }.bind(this)).fail(function () {
                    this.isPasswordVisible(true);
                    checkoutData.setCheckedEmailValue(this.email());
                }.bind(this)).always(function () {
                    this.isLoading(false);
                }.bind(this));
            },

            /**
             * Local email validation.
             *
             * @param {Boolean} focused - input focus.
             * @returns {Boolean} - validation result.
             */
            validateEmail: function (focused) {
                var loginFormSelector = 'form[data-role=email-with-possible-login]',
                    usernameSelector = loginFormSelector + ' input[name=username]',
                    loginForm = $(loginFormSelector),
                    validator,
                    valid;
    
                loginForm.validation();
    
                if (focused === false && !!this.email()) {
                    valid = !!$(usernameSelector).valid();
    
                    if (valid) {
                        $(usernameSelector).removeAttr('aria-invalid aria-describedby');
                    }
    
                    return valid;
                }
    
                validator = loginForm.validate();
    
                return validator.check(usernameSelector);
            },

            /**
             * Log in form submitting callback.
             *
             * @param {HTMLElement} loginForm - form element.
             */
            login: function (loginForm) {
                var loginData = {},
                    formDataArray = $(loginForm).serializeArray();
                    $('.captcha-reload').trigger( "click" );
    
                formDataArray.forEach(function (entry) {
                    loginData[entry.name] = entry.value;
                });
    
                if (this.isPasswordVisible() && $(loginForm).validation() && $(loginForm).validation('isValid')) {
                    fullScreenLoader.startLoader();
                    loginAction(loginData).always(function () {
                        fullScreenLoader.stopLoader();
                    });
                }
            },

            /**
             * Resolves an initial state of a login form.
             *
             * @returns {Boolean} - initial visibility state.
             */
            resolveInitialPasswordVisibility: function () {
                if (checkoutData.getInputFieldEmailValue() !== '' && checkoutData.getCheckedEmailValue() === '') {
                    return true;
                }
    
                if (checkoutData.getInputFieldEmailValue() !== '') {
                    return checkoutData.getInputFieldEmailValue() === checkoutData.getCheckedEmailValue();
                }
    
                return false;
            }

        });
    }
});
