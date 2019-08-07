<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

/**
 * Class Authenticate Middleware
 *
 * Checks if User is authenticated. If not redirect to login
 *
 * @package app\framework\Component\Auth
 */
class Authenticate
{
    public function handle()
    {
        if (! Auth::check()) {
            header("Location: /login");
            exit();
        }
    }
}
