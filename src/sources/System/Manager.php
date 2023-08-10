<?php

namespace IPS\stripeverification\System;

use Stripe\StripeClient;

class _Manager extends \IPS\Patterns\Singleton
{
    /**
     * @return StripeClient
     */
    public function stripeClient()
    {
        return new StripeClient(\IPS\Settings::i()->stripeverification_secret_key);
    }

    /**
     * @return bool
     */
    public function canStartVerificationProcess()
    {
        return ! empty(\IPS\Settings::i()->stripeverification_secret_key) ||
            ! empty(\IPS\Settings::i()->stripeverification_publishable_key) ||
            ! empty(\IPS\Settings::i()->stripeverification_webhook_secret);
    }

    /**
     * @return string|null
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function startVerificationProcess()
    {
        $session = $this->stripeClient()->identity->verificationSessions->create([
            'type' => 'document',
            'metadata' => [
                'member_id' => \IPS\Member::loggedIn()->member_id,
                'member_email' => \IPS\Member::loggedIn()->email,
            ],
        ]);

        return $session->client_secret;
    }
}
