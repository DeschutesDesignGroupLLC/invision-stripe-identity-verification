<?php

namespace IPS\stripeverification\modules\admin\system;

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
    /**
     * Execute
     *
     * @return	void
     */
    public function execute()
    {
        \IPS\Dispatcher::i()->checkAcpPermission('settings_manage');
        parent::execute();
    }

    /**
     * ...
     *
     * @return	void
     */
    protected function manage()
    {
        $form = new \IPS\Helpers\Form;

        $groups = [];
        foreach (\IPS\Member\Group::groups(true, false) as $group) {
            $groups[$group->g_id] = $group->name;
        }

        $form->addTab('stripeverification_stripe_settings_tab');
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
            $form->addTab('stripeverification_commerce_settings_tab');
            $form->addHeader(\IPS\Member::loggedIn()->language()->addToStack('stripeverification_commerce_settings'));
            $form->add(new \IPS\Helpers\Form\YesNo('stripeverification_commerce_enabled', \IPS\Settings::i()->stripeverification_commerce_enabled));
            $form->add(new \IPS\Helpers\Form\Node('stripeverification_commerce_subscription', \IPS\Settings::i()->stripeverification_commerce_subscription, false, [
                'class' => \IPS\nexus\Subscription\Package::class,
            ]));
        }

        if ($form->values()) {
            $form->saveAsSettings();
        }

        \IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('settings');
        \IPS\Output::i()->output = $form;
    }
}
