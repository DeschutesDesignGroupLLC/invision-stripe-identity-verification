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
        return $this->fetchLicenseStatus();
    }

    public function resetLicenseKeyData(): void
    {
        Settings::i()->changeValues([
            'stripeverification_license_key' => null,
            'stripeverification_license_status' => false,
            'stripeverification_license_fetched' => null,
            'stripeverification_license_status_payload' => null,
            'stripeverification_license_instance' => null,
            'stripeverification_license_activation_payload' => null,
        ]);
    }

    public function fetchLicenseStatus(bool $force = false, string $licenseKey = null): bool
    {
        if (! $force && Settings::i()->stripeverification_license_fetched > (time() - 1814400)) {
            return (bool) Settings::i()->stripeverification_license_status;
        }

        $licenseKey = $licenseKey ?? Settings::i()->stripeverification_license_key;

        if (! $licenseKey) {
            return false;
        }

        Settings::i()->changeValues([
            'stripeverification_license_status' => false,
            'stripeverification_license_fetched' => null,
            'stripeverification_license_status_payload' => null,
        ]);

        if (! Settings::i()->stripeverification_license_instance) {
            $response = $this->activateLicense($licenseKey);

            if (array_key_exists('activated', $response) && $response['activated'] === false) {
                return false;
            }
        }

        $response = Url::external('https://api.lemonsqueezy.com/v1/licenses/validate')
            ->request()
            ->setHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                json_encode([
                    'license_key' => $licenseKey,
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

        Log::debug("Fetched license key data. Payload: $payload", 'stripeverification');

        return $valid;
    }

    protected function activateLicense(string $licenseKey): ?array
    {
        $response = Url::external('https://api.lemonsqueezy.com/v1/licenses/activate')
            ->request()
            ->setHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                json_encode([
                    'license_key' => $licenseKey,
                    'instance_name' => Settings::i()->base_url,
                ])
            );

        $content = $response->decodeJson();

        $payload = json_encode($content);

        Settings::i()->changeValues([
            'stripeverification_license_instance' => $content['instance']['id'] ?? null,
            'stripeverification_license_activation_payload' => $payload,
        ]);

        Log::debug("Activated license key. Payload: $payload", 'stripeverification');

        return $content;
    }
}
