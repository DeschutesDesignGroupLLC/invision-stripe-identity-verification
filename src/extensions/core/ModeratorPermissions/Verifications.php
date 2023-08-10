<?php
/**
 * @brief		Moderator Permissions
 *
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 *
 * @since		08 Jun 2023
 */

namespace IPS\stripeverification\extensions\core\ModeratorPermissions;

/* To prevent PHP errors (extending class does not exist) revealing path */
if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0').' 403 Forbidden');
    exit;
}

/**
 * Moderator Permissions
 */
class _Verifications
{
    /**
     * Get Permissions
     *
     * @code
         );
     * @endcode
     */
    public function getPermissions(): array
    {
        return [
            'can_manage_verifications' => 'YesNo',
        ];
    }

    /**
     * Pre-save
     *
     * @note	This can be used to adjust the values submitted on the form prior to saving
     *
     * @param  array  $values		The submitted form values
     * @return	void
     */
    public function preSave(&$values)
    {

    }

    /**
     * After change
     *
     * @param  array  $moderator	The moderator
     * @param  array  $changed	Values that were changed
     * @return	void
     */
    public function onChange($moderator, $changed)
    {

    }

    /**
     * After delete
     *
     * @param  array  $moderator	The moderator
     * @return	void
     */
    public function onDelete($moderator)
    {

    }
}
