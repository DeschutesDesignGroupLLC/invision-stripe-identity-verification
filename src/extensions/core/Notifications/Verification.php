<?php
/**
 * @brief		Notification Options
 *
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 *
 * @since		07 Jun 2023
 */

namespace IPS\stripeverification\extensions\core\Notifications;

/* To prevent PHP errors (extending class does not exist) revealing path */
if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0').' 403 Forbidden');
    exit;
}

/**
 * Notification Options
 */
class _Verification
{
    /**
     * Get fields for configuration
     *
     * @param  \IPS\Member|null  $member		The member (to take out any notification types a given member will never see) or NULL if this is for the ACP
     */
    public static function configurationOptions(\IPS\Member $member = null): array
    {
        return [
            'stripeverification' => [
                'type' => 'standard',
                'notificationTypes' => ['verified', 'unverified'],
                'default' => ['inline'],
                'disabled' => ['email', 'push'],
                'description' => 'notifications__stripeverification_Verification_desc',
            ],
        ];
    }

    /**
     * Parse notification: verified
     *
     * @param  \IPS\Notification\Inline  $notification	The notification
     * @param  bool  $htmlEscape		TRUE to escape HTML in title
     *
     * @code
     );
     * @endcode
     */
    public function parse_verified(\IPS\Notification\Inline $notification, $htmlEscape = true): array
    {
        return [
            'title' => \IPS\Member::loggedIn()->language()->addToStack('stripeverification_notification_verified'),
            'url' => \IPS\Http\Url::internal(''),
            'content' => \IPS\Member::loggedIn()->language()->addToStack('stripeverification_notification_verified_message'),
            'author' => \IPS\Member::loggedIn(),
        ];
    }

    /**
     * Parse notification: verified
     *
     * @param  \IPS\Notification\Inline  $notification	The notification
     * @param  bool  $htmlEscape		TRUE to escape HTML in title
     *
     * @code
    );
     * @endcode
     */
    public function parse_unverified(\IPS\Notification\Inline $notification, $htmlEscape = true): array
    {
        return [
            'title' => \IPS\Member::loggedIn()->language()->addToStack('stripeverification_notification_unverified'),
            'url' => \IPS\Http\Url::internal(''),
            'content' => \IPS\Member::loggedIn()->language()->addToStack('stripeverification_notification_unverified_message'),
            'author' => \IPS\Member::loggedIn(),
        ];
    }
}
