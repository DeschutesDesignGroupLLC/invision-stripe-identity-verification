<?php

namespace IPS\stripeverification\System;

use Stripe\Webhook;
use Stripe\WebhookSignature;
use Symfony\Component\HttpFoundation\Request;

class _Webhook extends \IPS\Patterns\Singleton
{
    /**
     * @return void
     *
     * @throws \Stripe\Exception\SignatureVerificationException
     */
    public function handleIncomingWebhookRequest()
    {
        $request = Request::createFromGlobals();

        WebhookSignature::verifyHeader(
            $request->getContent(),
            $request->headers->get('Stripe-Signature'),
            \IPS\Settings::i()->stripeverification_webhook_secret
        );

        $event = Webhook::constructEvent(
            $request->getContent(),
            $request->headers->get('Stripe-Signature'),
            \IPS\Settings::i()->stripeverification_webhook_secret
        );

        switch ($event->type) {
            case 'identity.verification_session.verified':
                $member = \IPS\Member::load($event->data->object->metadata->member_id);
                $member->markMemberVerified();

                break;

            default:
                break;
        }
    }
}
