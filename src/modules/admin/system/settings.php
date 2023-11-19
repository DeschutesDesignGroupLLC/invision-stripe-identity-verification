<?php

namespace IPS\stripeverification\modules\admin\system;

use IPS\Helpers\Form\Codemirror;
use IPS\Helpers\Form\Text;
use IPS\Helpers\Form\YesNo;
use IPS\Http\Url;
use IPS\Output;
use IPS\Settings;
use IPS\stripeverification\Manager\LicenseKey;

/* To prevent PHP errors (extending class does not exist) revealing path */
if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0').' 403 Forbidden');
    exit;
}

/**
 * settings
 */
class _settings extends \IPS\Dispatcher\Controller
{
    public static bool $csrfProtected = true;

    public function execute(): void
    {
        \IPS\Dispatcher::i()->checkAcpPermission('settings_manage');
        parent::execute();
    }

    protected function manage(): void
    {
        $form = new \IPS\Helpers\Form;

        $groups = [];
        foreach (\IPS\Member\Group::groups(true, false) as $group) {
            $groups[$group->g_id] = $group->name;
        }

        $form->addTab('stripeverification_license');
        $form->add(new Text('stripeverification_license_key', Settings::i()->stripeverification_license_key, true, [], function ($value) {
            if (! LicenseKey::i()->fetchLicenseStatus(true, $value)) {
                throw new \DomainException('The license key you entered is not valid.');
            }
        }));

        $form->addTab('stripeverification_stripe_settings');
        $form->addHeader(\IPS\Member::loggedIn()->language()->addToStack('stripeverification_stripe_settings'));
        $form->add(new \IPS\Helpers\Form\Text('stripeverification_publishable_key', \IPS\Settings::i()->stripeverification_publishable_key, true));
        $form->add(new \IPS\Helpers\Form\Text('stripeverification_secret_key', \IPS\Settings::i()->stripeverification_secret_key, true));
        $form->add(new \IPS\Helpers\Form\Text('stripeverification_webhook_secret', \IPS\Settings::i()->stripeverification_webhook_secret, true));

        $form->addHeader(\IPS\Member::loggedIn()->language()->addToStack('stripeverification_icon_settings'));
        $form->add(new \IPS\Helpers\Form\Text('stripeverification_icon', \IPS\Settings::i()->stripeverification_icon, true, [
            'placeholder' => 'fa fa-solid fa-circle-check',
        ]));
        $form->add(new \IPS\Helpers\Form\Color('stripeverification_icon_color', \IPS\Settings::i()->stripeverification_icon_color, true));

        $form->addHeader(\IPS\Member::loggedIn()->language()->addToStack('stripeverification_verification_settings'));
        $form->add(new \IPS\Helpers\Form\Select('stripeverification_verification_group', explode(',', \IPS\Settings::i()->stripeverification_verification_group), false, [
            'options' => $groups,
            'multiple' => true,
        ]));

        if (\IPS\Application::appIsEnabled('nexus')) {
            $form->addTab('stripeverification_commerce_settings');
            $form->addHeader(\IPS\Member::loggedIn()->language()->addToStack('stripeverification_commerce_settings'));
            $form->add(new \IPS\Helpers\Form\YesNo('stripeverification_commerce_enabled', \IPS\Settings::i()->stripeverification_commerce_enabled));
            $form->add(new \IPS\Helpers\Form\Node('stripeverification_commerce_subscription', \IPS\Settings::i()->stripeverification_commerce_subscription, false, [
                'class' => \IPS\nexus\Subscription\Package::class,
            ]));
        }

        $form->addTab('stripeverification_debug');
        $form->addMessage('stripeverification_license_data_message');
        $form->add(new YesNo('stripeverification_license_status', LicenseKey::i()->isValid(), false, ['disabled' => true]));
        $form->add(new Text('stripeverification_license_fetched', Settings::i()->stripeverification_license_fetched ? date('m/d/Y h:i A', (int) Settings::i()->stripeverification_license_fetched) : null, false, ['disabled' => true]));
        $form->add(new Text('stripeverification_license_instance', Settings::i()->stripeverification_license_instance, false, ['disabled' => true]));
        $form->add(new Codemirror('stripeverification_license_status_payload', json_encode(json_decode(Settings::i()->stripeverification_license_status_payload), JSON_PRETTY_PRINT), false, ['disabled' => true, 'mode' => 'json']));
        $form->add(new Codemirror('stripeverification_license_activation_payload', json_encode(json_decode(Settings::i()->stripeverification_license_activation_payload), JSON_PRETTY_PRINT), false, ['disabled' => true, 'mode' => 'json']));

        if ($form->values()) {
            $form->saveAsSettings();
        }

        \IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('settings');
        Output::i()->sidebar['actions']['refresh'] = [
            'icon' => 'refresh',
            'link' => Url::internal('app=stripeverification&module=system&controller=settings&do=refresh'),
            'title' => 'stripeverification_license_refresh_title',
        ];
        Output::i()->sidebar['actions']['reset'] = [
            'icon' => 'trash',
            'link' => Url::internal('app=stripeverification&module=system&controller=settings&do=reset'),
            'title' => 'stripeverification_license_reset_title',
        ];
        \IPS\Output::i()->output = $form;
    }

    protected function refresh(): void
    {
        LicenseKey::i()->fetchLicenseStatus(true);

        Output::i()->redirect(Url::internal('app=stripeverification&module=system&controller=settings'), 'stripeverification_license_refreshed');
    }

    protected function reset(): void
    {
        LicenseKey::i()->resetLicenseKeyData();

        Output::i()->redirect(Url::internal('app=stripeverification&module=system&controller=settings'), 'stripeverification_license_reset');
    }
}
