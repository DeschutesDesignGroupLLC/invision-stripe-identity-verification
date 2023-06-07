<?php

namespace IPS\stripeverification\modules\front\system;

/* To prevent PHP errors (extending class does not exist) revealing path */

if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0').' 403 Forbidden');
    exit;
}

/**
 * verification
 */
class _verification extends \IPS\Dispatcher\Controller
{
    /**
     * @return void
     */
    public function execute()
    {
        if (! \IPS\stripeverification\System\Manager::i()->canStartVerificationProcess()) {
            \IPS\Output::i()->error('stripeverification_error_cannot_start_verification', '1APP/1', 500);
        }

        parent::execute();
    }

    /**
     * @return void
     */
    protected function manage()
    {
        \IPS\Output::i()->jsFiles = array_merge(\IPS\Output::i()->jsFiles, \IPS\Output::i()->js('front_verification.js', 'stripeverification', 'front'));
        \IPS\Output::i()->output = \IPS\Theme::i()->getTemplate('verification', 'stripeverification', 'front')->modal(\IPS\Member::loggedIn());
    }

    /**
     * @return void
     */
    protected function key()
    {
        \IPS\Output::i()->json([
            'key' => (string) \IPS\Settings::i()->stripeverification_publishable_key,
        ]);
    }

    /**
     * @return void
     */
    protected function start()
    {
        \IPS\Output::i()->json([
            'secret' => (string) \IPS\stripeverification\System\Manager::i()->startVerificationProcess(),
        ]);
    }

    /**
     * @return void
     */
    protected function verify()
    {
        $member = \IPS\Member::load(\IPS\Request::i()->member);

        $member->markMemberVerified();

        \IPS\Output::i()->redirect(\IPS\Http\Url::internal('app=core&module=modcp&controller=modcp&tab=stripeverification', 'front', 'modcp_stripeverification'), 'modcp_stripeverification_verified');
    }

    /**
     * @return void
     */
    protected function unverify()
    {
        $member = \IPS\Member::load(\IPS\Request::i()->member);

        $member->markMemberUnverified();

        \IPS\Output::i()->redirect(\IPS\Http\Url::internal('app=core&module=modcp&controller=modcp&tab=stripeverification', 'front', 'modcp_stripeverification'), 'modcp_stripeverification_unverified');
    }
}