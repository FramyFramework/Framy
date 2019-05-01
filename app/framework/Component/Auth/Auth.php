<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

use app\framework\Component\Database\Model\Model;
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;

class Auth
{
    /**
     * Get the logged in user model
     *
     * @return Model|null
     * @throws StringObjectException
     */
    public static function user()
    {
        return SessionGuard::getInstance()->user();
    }

    /**
     * Check if someone is logged in
     *
     * @return bool
     * @throws StringObjectException
     */
    public static function check(): bool
    {
        return static::user() !== null;
    }

    /**
     * Get the currently authenticated user's ID...
     *
     * @return int
     * @throws StringObjectException
     */
    public static function id():int
    {
        return static::user()->id;
    }
}
