<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

use app\framework\Component\Routing\Request;
use app\framework\Component\Routing\Response;
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
     * @param       $errors
     * @param array $oldValues
     * @return Response
     */
    public function showRegistrationForm($errors = null, array $oldValues = [])
    {
        return view('auth/register', ['errors' => $errors, 'old' => $oldValues]);
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
        $params = $request->params();

        /** @var ArrayObject $errors */
        $errors = $this->validator($params);

        // handle if errors appear
        $errors->removeIfValue(true);
        if ($errors->count() > 0) {
            return $this->showRegistrationForm($errors, $params);
        }

        // call create function
        $this->create($params);

        // login
        $this->login($request);

        // redirect
        header("Location: ".$this->redirectTo);
        exit;
    }
}
