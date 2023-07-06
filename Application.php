<?php
/**
 * @brief		Stripe Identity Verification Application Class
 *
 * @author		<a href='https://www.deschutesdesigngroup.com'>Deschutes Design Group LLC</a>
 * @copyright	(c) 2023 Deschutes Design Group LLC
 *
 * @since		07 Jun 2023
 *
 * @version
 */

namespace IPS\stripeverification;

/**
 * Stripe Identity Verification Application Class
 */
class _Application extends \IPS\Application
{
    /**
     * _Application constructor.
     */
    public function __construct()
    {
        require_once static::getRootPath().'/applications/stripeverification/sources/vendor/autoload.php';
    }

    /**
     * @return string
     */
    protected function get__icon()
    {
        return 'cc-stripe';
    }
}
