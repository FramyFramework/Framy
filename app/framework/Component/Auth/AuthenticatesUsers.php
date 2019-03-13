<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

use app\framework\Component\Database\DB;
use app\framework\Component\Database\Model\Model;
use app\framework\Component\EventManager\EventManagerTrait;
use app\framework\Component\Hashing\Hash;
use app\framework\Component\Route\Klein\Request;
use app\framework\Component\Route\Klein\Response;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;
use app\framework\Component\StdLib\StdObject\StdObjectException;
use app\framework\Component\StdLib\StdObject\StringObject\StringObject;

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
        $remember = is_null($request->param("remember")) ? false : $request->param("remember");

        //validate fields
        /** @var ArrayObject $errors */
        $errors = $this->validator($credentials);

        $errors->removeIfValue(true);

        // TODO remove this as soon as the validate method looses backwards capability needs
        $errors->removeKey("name");

        if ($errors->count() > 0) {
            return $this->showLoginForm($errors);
        }

        //check for to many tries
        if ($this->attempt($credentials, $remember)) {
            // handle user was authenticated
            header("Location: ".$this->redirectTo);
            exit;
        }
        //TODO: increase failed attempt count

        $errors->append("falseCredentials", "The entered credentials are false");
        return $this->showLoginForm($errors);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @param bool $login
     * @return bool
     */
    protected function attempt(array $credentials = [], bool $remember = false, $login = true): bool
    {
        $this->fireAttemptingEvent($credentials, $remember, $login);

        $this->lastAttempted = $user = $this->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            if ($login) {
                //save user in session
                $_SESSION[$this->getName()] = $user->id;

                if ($remember) {
                    //createRememberTokenIfDoesntExist
                    $this->createRememberTokenIfDoesntExist($user);

                    //save in cookie
                    $value = $user->id."|".$user->remember_token;
                    setcookie("remember_session_".sha1(get_class($this)), $value);
                }

                $this->fireLoginEvent($user, $remember);
            }

            return true;
        }

        return false;
    }

    protected function hasValidCredentials($user, $credentials)
    {
        return !is_null($user) && Hash::check($credentials['password'], $user->password);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credential
     * @return Model|null
     */
    protected function retrieveByCredentials(array $credential)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // generic "user" object that will be utilized by the Guard instances.
        $query = "SELECT * FROM users WHERE ";

        $i = 0;
        foreach ($credential as $key => $value) {
            try {
                $key = new StringObject($key);
            } catch (StdObjectException $e) {
                handle($e);
            }

            if (! $key->contains("password")) {
                $prepend = (0 < $i ? ", " : "");

                $query  .= $prepend. $key ."='". $value."'";

                $i++;
            }
        }

        return DB::select($query)[0];
    }

    protected function createRememberTokenIfDoesntExist(&$user)
    {
        if (! isset($user->remember_token)) {
            $this->createRememberToken($user);
        }
    }

    protected function createRememberToken(&$user)
    {
        $token = StringObject::random(60);
        DB::update("UPDATE users SET remember_token=:token WHERE id=:id", [
            'token' => $token,
            'id' => $user->id
        ]);

        $user->rember_token = $token;
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'login_session_'.sha1(get_class($this));
    }

    protected function fireAttemptingEvent(array $credentials = [], bool $remember = false, $login = true)
    {
        $this->eventManager()->fire("auth.attempting", [
            'credentials' => $credentials,
            'remember' => $remember,
            'login' => $login
        ]);
    }

    protected function fireLoginEvent($user, $remember)
    {
        $this->eventManager()->fire("auth.login", [
            'user' => $user,
            'remember' => $remember,
        ]);
    }
}
