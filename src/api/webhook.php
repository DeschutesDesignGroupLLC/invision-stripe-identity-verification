<?php

namespace IPS\stripeverification\api;

use Exception;

/* To prevent PHP errors (extending class does not exist) revealing path */
if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0').' 403 Forbidden');
    exit;
}

/**
 * @brief	Stripe Identity Verification Webhook
 */
class _webhook extends \IPS\Api\Controller
{
    /**
     * POST /stripeverification/webhook
     * Webhook for processing Stripe events.
     *
     * @return		null
     */
    public function POSTindex()
    {
        try {
            \IPS\stripeverification\System\Webhook::i()->handleIncomingWebhookRequest();
        } catch (Exception $exception) {
            return new \IPS\Api\Response(200, [
                'error' => \get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'line' => $exception->getLine(),
            ]);
        }

        return new \IPS\Api\Response(204, '');
    }
}
