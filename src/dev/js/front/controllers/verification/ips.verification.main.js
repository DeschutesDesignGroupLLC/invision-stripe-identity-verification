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

            self.submissionMessage =

            ips.loader.get(['https://js.stripe.com/v3/']).then( function () {
                ips.getAjax()('?app=stripeverification&module=system&controller=verification&do=key').done(function(response, status, jqXHR) {
                    self.stripeClient = Stripe(response.key);
                });
            });
        },

        startVerification: function (e) {
            var self = this;

            var verificationButton = $(e.currentTarget);
            verificationButton.text('Loading...');

            var submittedMessage = verificationButton.closest('div').find('#stripeverification_submit_message');
            var infoMessage = verificationButton.closest('div').find('#stripeverification_info_message');

            ips.getAjax()('?app=stripeverification&module=system&controller=verification&do=start').done(function(response, status, jqXHR) {
                return self.stripeClient.verifyIdentity(response.secret).then(function (results) {
                    if (results.error) {
                        verificationButton.text('Start Verification');
                    } else {
                        ips.getAjax()('?app=stripeverification&module=system&controller=verification&do=finish')
                        verificationButton.hide();
                        infoMessage.hide();
                        submittedMessage.show();
                    }
                });
            });
        }
    });
}(jQuery, _));