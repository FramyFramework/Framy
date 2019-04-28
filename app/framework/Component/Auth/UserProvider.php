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
use app\framework\Component\Database\Model\Model;
use app\framework\Component\StdLib\StdObject\StdObjectException;
use app\framework\Component\StdLib\StdObject\StringObject\StringObject;

/**
 * Class UserProvider
 * @package app\framework\Component\Auth
 */
class UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return Model|null
     */
    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return Model|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // TODO
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  Model  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Model $user, $token)
    {
        // TODO
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return Model|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // generic "user" object that will be utilized by the Guard instances.
        $query = "SELECT * FROM users WHERE ";

        $i = 0;
        foreach ($credentials as $key => $value) {
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

    /**
     * Validate a user against the given credentials.
     *
     * @param  Model  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Model $user, array $credentials)
    {
        // TODO
    }
}
