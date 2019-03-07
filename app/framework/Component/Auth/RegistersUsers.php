<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;


use app\framework\Component\Route\Klein\Request;
use app\framework\Component\Route\Klein\Response;

trait RegistersUsers
{
    /**
     * Show the application registration form.
     *
     * @return Response
     */
    public function getRegister()
    {
        return $this->showRegistrationForm();
    }

    /**
     * Show the application registration form.
     *
     * @return Response
     */
    public function showRegistrationForm()
    {
        return view('auth/register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postRegister(Request $request)
    {
        return $this->register($request);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        $errors = $this->validator($request->params());

        
    }
}
