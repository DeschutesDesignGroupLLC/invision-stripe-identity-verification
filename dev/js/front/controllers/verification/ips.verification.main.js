;( function($, _, undefined){
    "use strict";

    ips.controller.register('stripeverification.front.verification.main', {
        stripeClient: false,

        initialize: function () {
            this.setup();
            this.on('click', '[data-verification-button]', this.startVerification);
        },

        setup: function () {
            var self = this;

            ips.loader.get(['https://js.stripe.com/v3/']).then( function () {
                ips.getAjax()('?app=stripeverification&module=system&controller=verification&do=key').done(function(response, status, jqXHR) {
                    self.stripeClient = Stripe(response.key)
                });
            });
        },

        startVerification: function () {
            var self = this;

            ips.getAjax()('?app=stripeverification&module=system&controller=verification&do=start').done(function(response, status, jqXHR) {
                return self.stripeClient.verifyIdentity(response.secret);
            });
        }
    });
}(jQuery, _));