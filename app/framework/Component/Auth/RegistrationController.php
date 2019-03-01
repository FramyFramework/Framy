<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

/**
 * Class RegistrationController
 * @package app\custom\Http\Controller
 */
class RegistrationController
{
    public function register($request)
    {

    }

    /**
     * Tell the user to check their email provider.
     */
    public function checkEmail()
    {

    }

    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param $request
     * @param string  $token
     *
     */
    public function confirmAction($request, $token)
    {

    }

    /**
     * Tell the user his account is now confirmed.
     */
    public function confirmedAction($request)
    {

    }
}
