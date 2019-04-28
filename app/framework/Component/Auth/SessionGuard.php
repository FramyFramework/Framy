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
use app\framework\Component\Http\Session;
use app\framework\Component\Routing\Request;
use app\framework\Component\StdLib\SingletonTrait;
use app\framework\Component\StdLib\StdObject\StringObject\StringObject;
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;
use Exception;

class SessionGuard
{
    use SingletonTrait,EventManagerTrait;

    /**
     * The currently authenticated user.
     */
    protected $user;

    /**
     * @var Session
     */
    protected $session;

    /**
     * The user provider implementation.
     *
     * @var UserProvider
     */
    protected $provider;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Indicates if a token user retrieval has been attempted.
     *
     * @var bool
     */
    protected $tokenRetrievalAttempted = false;


    /**
     *
     */
    public function init()
    {
        $this->session  = new Session();
        $this->provider = new UserProvider();

        $this->request = Request::createFromGlobals();
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     * @throws StringObjectException
     */
    public function check()
    {
        return ! is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     * @throws StringObjectException
     */
    public function guest()
    {
        return ! $this->check();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     * @throws StringObjectException
     */
    public function id()
    {
        if ($this->user()) {
            return $this->user()->id;
        }
    }

    /**
     * Update the session with the given ID.
     *
     * @param  string  $id
     * @return void
     */
    protected function updateSession($id)
    {
        $this->session->set($this->getName(), $id);
    }

    /**
     * Get the currently authenticated user.
     * @return Model|null
     * @throws StringObjectException
     */
    public function user()
    {
        if ($this->loggedOut) {
            return null;
        }

        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }

        $id = $this->session->get($this->getName());

        // First we will try to load the user using the identifier in the session if
        // one exists. Otherwise we will check for a "remember me" cookie in this
        // request, and if one exists, attempt to retrieve the user using that.
        $user = null;

        if (! is_null($id)) {
            $user = $this->provider->retrieveById($id);
        }

        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        $recaller = $this->getRecaller();

        if (is_null($user) && ! is_null($recaller)) {
            $user = $this->getUserByRecaller($recaller);

            if ($user) {
                $this->updateSession($user->id);

                $this->fireLoginEvent($user, true);
            }
        }

        return $this->user = $user;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @param bool $login
     * @return bool
     * @throws StringObjectException
     */
    public function attempt(array $credentials = [], bool $remember = false, $login = true): bool
    {
        $this->fireAttemptingEvent($credentials, $remember, $login);

        $this->lastAttempted = $this->user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($this->user, $credentials)) {
            if ($login) {
                $this->login($this->user, $remember);
            }

            return true;
        }

        return false;
    }

    /**
     *
     */
    protected function createRememberTokenIfDoesntExist()
    {
        if (! isset($this->user->remember_token)) {
            $this->refreshRememberToken();
        }
    }

    /**
     * Remove the user data from the session and cookies.
     *
     * @return void
     */
    protected function clearUserDataFromStorage()
    {
        $this->session->remove($this->getName());

        if (! is_null($this->getRecaller())) {
            $recaller = $this->getRecallerName();

            setcookie($recaller, null, -1, '/');
        }
    }

    /**
     * @throws Exception
     */
    protected function refreshRememberToken()
    {
        $this->user->rember_token = $token = StringObject::random(60);

        DB::update("UPDATE users SET remember_token=:token WHERE id=:id", [
            'token' => $token,
            'id' => $this->user->id
        ]);
    }

    /**
     * @param $user
     * @param $remember
     * @throws StringObjectException
     */
    public function login($user, $remember)
    {
        $this->session->set($this->getName(), $user->id);

        if ($remember) {
            $this->createRememberTokenIfDoesntExist();

            $value = $user->id."|".$user->remember_token;
            setcookie("remember_session_".sha1(get_class($this)), $value);
        }

        $this->fireLoginEvent($user, $remember);
    }

    /**
     * @throws Exception
     */
    public function logout()
    {
        $user = $this->user();

        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        $this->clearUserDataFromStorage();

        if (! is_null($this->user)) {
            $this->refreshRememberToken();
        }

        //TODO: fire logout event

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * @param array $credentials
     * @param bool $remember
     * @param bool $login
     * @throws StringObjectException
     */
    protected function fireAttemptingEvent(array $credentials = [], bool $remember = false, $login = true)
    {
        $this->eventManager()->fire("auth.attempting", [
            'credentials' => $credentials,
            'remember' => $remember,
            'login' => $login
        ]);
    }

    /**
     * @param $user
     * @param $remember
     * @throws StringObjectException
     */
    protected function fireLoginEvent($user, $remember)
    {
        $this->eventManager()->fire("auth.login", [
            'user' => $user,
            'remember' => $remember,
        ]);
    }

    /**
     * @param $recaller
     * @return Model|null
     * @throws StringObjectException
     */
    public function getUserByRecaller($recaller)
    {
        if ($this->validRecaller($recaller) && ! $this->tokenRetrievalAttempted) {
            $this->tokenRetrievalAttempted = true;

            list($id, $token) = explode('|', $recaller, 2);

            $this->viaRemember = ! is_null($user = $this->provider->retrieveByToken($id, $token));

            return $user;
        }
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

    /**
     * @return mixed
     */
    public function getRecaller()
    {
        return $this->request->cookies()->get($this->getRecallerName());
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'remember_session_'.sha1(get_class($this));
    }

    /**
     * Check if user credentials are valid
     *
     * @param $user
     * @param $credentials
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return !is_null($user) && Hash::check($credentials['password'], $user->password);
    }

    /**
     * Determine if the recaller cookie is in a valid format.
     *
     * @param  mixed  $recaller
     * @throws StringObjectException
     * @return bool
     */
    protected function validRecaller($recaller)
    {
        if (! is_string($recaller) || ! (new StringObject($recaller))->contains('|')) {
            return false;
        }

        $segments = explode('|', $recaller);

        return count($segments) == 2 && trim($segments[0]) !== '' && trim($segments[1]) !== '';
    }
}
