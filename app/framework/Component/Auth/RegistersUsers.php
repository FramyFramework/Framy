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
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;

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
     * @param $errors
     * @return Response
     */
    public function showRegistrationForm($errors = null)
    {
        return view('auth/register', ['errors' => $errors]);
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
        /** @var ArrayObject $errors */
        $errors = $this->validator($request->params());

        // handle if errors appear
        $errors->removeIfValue(true);
        if ($errors->count() > 0) {
            return $this->showRegistrationForm($errors);
        }

        // call create function
        $this->create($request->params());

        //TODO: user should be logged in automatically

        // redirect
        header("Location: ".$this->redirectTo);
        exit;
    }
}
