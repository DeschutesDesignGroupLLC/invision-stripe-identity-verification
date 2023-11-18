<?php

namespace IPS\stripeverification\Manager;

use IPS\Http\Url;
use IPS\Log;
use IPS\Patterns\Singleton;
use IPS\Settings;

class _LicenseKey extends Singleton
{
    public function isValid(): bool
    {
        if (! Settings::i()->stripeverification_license_instance) {
            $this->activateLicense();
        }

        if (! Settings::i()->stripeverification_license_fetched || ! Settings::i()->stripeverification_license_status || Settings::i()->stripeverification_license_fetched < (time() - 1814400)) {
            $this->fetchLicenseStatus();
        }

        return (bool) Settings::i()->stripeverification_license_status;
    }

    public function fetchLicenseStatus(): bool
    {
        $response = Url::external('https://api.lemonsqueezy.com/v1/licenses/validate')
            ->request()
            ->setHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                json_encode([
                    'license_key' => Settings::i()->stripeverification_license_key,
                    'instance_id' => Settings::i()->stripeverification_license_instance,
                ])
            );

        $content = $response->decodeJson();

        $valid = $response->isSuccessful() && array_key_exists('valid', $content) && $content['valid'] === true;

        $payload = json_encode($content);

        Settings::i()->changeValues([
            'stripeverification_license_status' => $valid,
            'stripeverification_license_fetched' => time(),
            'stripeverification_license_status_payload' => $payload,
        ]);

        Log::log("Fetched license key data. Payload: $payload", 'stripeverification');

        return $valid;
    }

    protected function activateLicense(): void
    {
        $response = Url::external('https://api.lemonsqueezy.com/v1/licenses/activate')
            ->request()
            ->setHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                json_encode([
                    'license_key' => Settings::i()->stripeverification_license_key,
                    'instance_name' => Settings::i()->base_url,
                ])
            );

        $content = $response->decodeJson();

        $payload = json_encode($content);

        Settings::i()->changeValues([
            'stripeverification_license_instance' => $content['instance']['id'] ?? null,
            'stripeverification_license_activation_payload' => $payload,
        ]);

        Log::log("Activated license key. Payload: $payload", 'stripeverification');
    }
}
