<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;


class Auth
{
    /**
     * Get the logged in user model
     * @return \app\framework\Component\Database\Model\Model|null
     */
    public static function user()
    {
        return SessionGuard::getInstance()->user();
    }

    /**
     * Check if someone is logged in
     * @return bool
     */
    public static function check(): bool
    {
        return static::user() !== null;
    }
}
