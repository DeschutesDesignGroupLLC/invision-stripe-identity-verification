<?php
/**
 * @brief		Moderator Control Panel Extension: Verification
 *
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 *
 * @since		07 Jun 2023
 */

namespace IPS\stripeverification\extensions\core\ModCp;

/* To prevent PHP errors (extending class does not exist) revealing path */
if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0').' 403 Forbidden');
    exit;
}

/**
 * @brief	Moderator Control Panel Extension: Verification
 */
class _Verification
{
    /**
     * Returns the primary tab key for the navigation bar
     *
     * @return	string|null
     */
    public function getTab()
    {
        if (! \IPS\Member::loggedIn()->modPermission('can_manage_verifications')) {
            return null;
        }

        return 'stripeverification';
    }

    /**
     * Manage
     *
     * @return	void
     */
    public function manage()
    {
        if (! \IPS\Member::loggedIn()->modPermission('can_manage_verifications')) {
            \IPS\Output::i()->error('no_module_permission', '1APP/2', 403);
        }

        $table = new \IPS\Helpers\Table\Db(\IPS\stripeverification\System\Verification::$databaseTable, \IPS\Http\Url::internal('app=core&module=modcp&controller=modcp&tab=stripeverification'));
        $table->tableTemplate = [\IPS\Theme::i()->getTemplate('tables', 'core', 'admin'), 'table'];
        $table->rowsTemplate = [\IPS\Theme::i()->getTemplate('tables', 'core', 'admin'), 'rows'];
        $table->include = ['member_id', 'verified', 'verified_at', 'submitted_at', 'verify'];
        $table->mainColumn = 'verified_at';
        $table->sortBy = 'verified_at';
        $table->sortDirection = 'desc';
        $table->sortOptions = ['verified_at'];
        $table->langPrefix = 'stripverification_';
        $table->widths = ['member_id' => 25, 'verified' => 15, 'verified_at' => 25, 'submitted_at' => 25];
        $table->title = \IPS\Member::loggedIn()->language()->addToStack('modcp_stripeverification');

        $table->parsers = [
            'member_id' => function ($val, $row) {
                $member = \IPS\Member::load($val);

                return \IPS\Theme::i()->getTemplate('global', 'core')->userPhoto($member, 'tiny').' '.$member->link();
            },
            'verified' => function ($val, $row) {
                $member = \IPS\Member::load($row['member_id']);

                return match (true) {
                    $member->verified => 'Verified',
                    $member->verification_processing => 'Processing',
                    ! $val => 'Not Verified',
                    default => 'Not Verified'
                };
            },
            'verified_at' => function ($val, $row) {
                return $val ? \IPS\DateTime::ts($val) : null;
            },
            'submitted_at' => function ($val, $row) {
                return $val ? \IPS\DateTime::ts($val) : null;
            },
            'verify' => function ($val, $row) {
                if ($row['verified']) {
                    return \IPS\Theme::i()->getTemplate('modcp', 'stripeverification', 'front')->unverifyButton($row['member_id']);
                }

                return \IPS\Theme::i()->getTemplate('modcp', 'stripeverification', 'front')->verifyButton($row['member_id']);
            },
        ];

        \IPS\Output::i()->sidebar['enabled'] = false;
        \IPS\Output::i()->breadcrumb[] = [null, \IPS\Member::loggedIn()->language()->addToStack('modcp_stripeverification')];
        \IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('modcp_stripeverification');

        return \IPS\Theme::i()->getTemplate('modcp', 'stripeverification', 'front')->verifications((string) $table);
    }
}
