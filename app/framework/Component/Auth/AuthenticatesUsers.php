<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

use app\framework\Component\Database\DB;
use app\framework\Component\Route\Klein\Request;
use app\framework\Component\StdLib\StdObject\StringObject\StringObject;

/**
 * Trait AuthenticatesUsers
 * @package app\custom\Http\Controller\Auth
 */
trait AuthenticatesUsers
{
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
     */
    public function showLoginForm()
    {
        return view("auth/login");
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     */
    public function postLogin(Request $request)
    {
        return $this->login($request);
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     */
    public function login(Request $request)
    {
        $credenctials = $this->getCredentials($request);

        //validate fields

        //check for to many tries

        $this->attempt($credenctials);
    }

    protected function getCredentials(Request $request)
    {
        return [
            'username' => $request->paramsPost()->get("_username"),
            'email' => $request->paramsPost()->get("_email"),
            'password' => $request->paramsPost()->get("_password")
        ];
    }

    /**
     * @param array $credentials
     * @return bool
     */
    protected function attempt(array $credentials = []): bool
    {
        //fire attempt event

        $this->lastAttempted = $user = $this->retrieveByCredentials($credentials);
    }

    protected function retrieveByCredentials(array $credential)
    {
        // TODO: retrieve db result by username and/or email rather which one is declared
    }
}
