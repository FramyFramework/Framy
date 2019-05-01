<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

use app\custom\Models\User;
use app\framework\Component\Database\DB;
use app\framework\Component\EventManager\EventManagerTrait;
use app\framework\Component\Hashing\Hash;
use app\framework\Component\Routing\Request;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;
use app\framework\Component\StdLib\StdObject\StringObject\StringObject;
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;

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
     * @param       $errors
     * @param array $oldValues
     * @return mixed
     */
    public function showLoginForm($errors = null, array $oldValues = [])
    {
        return view("auth/login", ["errors" => $errors, 'old' => $oldValues]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return mixed
     * @throws StringObjectException
     */
    public function postLogin(Request $request)
    {
        return $this->login($request);
    }

    public function getPasswordReset()
    {
        // check if user is authenticated redirect to login if not
        if(! Auth::check()) {
            header("Location: /login");
            exit;
        }

        // create reset token
        $token = StringObject::random(100);

        $user = Auth::user();
        $user->reset_password_token = $token;
        $user->save();

        //TODO send mail with token link to user email
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return mixed
     * @throws StringObjectException
     */
    public function login(Request $request)
    {
        $credentials = $request->paramsPost()->all();

        // gets passed from register flow. So we need to
        // remove it to successfully login. I dont understand
        // why but that is how we do it
        unset($credentials['name']);
        unset($credentials['remember']);

        $remember = $request->param("remember") ?: false;

        /** @var ArrayObject $errors */
        $errors = $this->validator($credentials);

        $errors->removeIfValue(true);

        //TODO: remove this as soon as the validate method looses backwards capability needs
        $errors->removeKey("name");

        if ($errors->count() > 0) {
            return $this->showLoginForm($errors, $credentials);
        }

        if ($this->getGuard()->attempt($credentials, $remember)) {
            // handle user was authenticated
            header("Location: ".$this->redirectTo);
            exit;
        }

        //TODO: increase failed attempt count

        $errors->append("falseCredentials", "The entered credentials are false");
        return $this->showLoginForm($errors, $credentials);
    }

    public function logout()
    {
        $this->getGuard()->logout();

        header("Location: ".$this->redirectAfterLogout);
        exit;
    }

    public function resetPassword(Request $request)
    {
        $token = $request->paramsNamed()->get("token");

        if (Auth::user()->reset_password_token !== $token) {
            // access denied
            return view("errors/403");
        }

        if ($request->method() === 'GET') {
            // show reset password view
            return view("auth/passwords/reset", ['token' => $token]);
        }

        $passwordConfirmation = $request->paramsPost()->get("password_confirmation");
        $password             = $request->paramsPost()->get("password");
        $errors               = arr([]);

        if ($password !== $passwordConfirmation) {
            $errors->append("password_confirmation", "Passwords do not match");
        }

        /** @var ArrayObject $errors */
        $errors->merge($this->validator([
            'name' => 'John Doe',
            'email' => 'a@b.c',
            'password' => $password
        ]));

        $errors->removeIfValue(true);

        if ($errors->count() > 0) {
            return view("auth/passwords/reset", ["errors" => $errors]);
        }

        // now everything is fine and the password can be set
        $user = Auth::user();
        $user->password = Hash::make($password);
        $user->reset_password_token = '';
        $user->save();

        header("Location: ".$this->redirectTo);
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
