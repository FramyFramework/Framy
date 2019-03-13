<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

use app\framework\Component\EventManager\EventManagerTrait;
use app\framework\Component\Route\Klein\Request;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;

/**
 * Trait AuthenticatesUsers
 * @package app\custom\Http\Controller\Auth
 */
trait AuthenticatesUsers
{
    use EventManagerTrait;

    /**
     * The user we last attempted to retrieve.
     *
     * @var string
     */
    protected $lastAttempted;

    /**
     * Show the application login form.
     */
    public function getLogin()
    {
        return $this->showLoginForm();
    }

    /**
     * Show the application login form.
     * @param $errors
     * @return mixed
     */
    public function showLoginForm($errors = null)
    {
        return view("auth/login", ["errors" => $errors]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        return $this->login($request);
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $credentials = $request->paramsPost()->all();
        unset($credentials['remember']);

        $remember = $request->param("remember") ?: false;

        /** @var ArrayObject $errors */
        $errors = $this->validator($credentials);

        $errors->removeIfValue(true);

        //TODO: remove this as soon as the validate method looses backwards capability needs
        $errors->removeKey("name");

        if ($errors->count() > 0) {
            return $this->showLoginForm($errors);
        }

        if ($this->getGuard()->attempt($credentials, $remember)) {
            // handle user was authenticated
            header("Location: ".$this->redirectTo);
            exit;
        }

        //TODO: increase failed attempt count

        $errors->append("falseCredentials", "The entered credentials are false");
        return $this->showLoginForm($errors);
    }

    public function logout()
    {
        $this->getGuard()->logout();

        //TODO: set where to redirect in AuthController
        header("Location: /");
        exit;
    }

    /**
     * @return SessionGuard
     */
    protected function getGuard()
    {
        return SessionGuard::getInstance();
    }
}
