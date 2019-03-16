<?php
namespace app\custom\Http\Controller\Auth;

use app\custom\Models\User;
use app\framework\Component\Auth\AuthenticatesAndRegistersUsers;
use app\framework\Component\Hashing\Hash;
use app\framework\Component\StdLib\Exception\Exception;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;

/**
 * Class AuthController
 * @package app\custom\Http\Controller\Auth
 */
class AuthController
{
   use AuthenticatesAndRegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Where to redirect users after logout.
     *
     * @var string
     */
    protected $redirectAfterLogout = "/";

    public function validator($data)
    {
        $errors = new ArrayObject([]);

        $errors->append('name', $this->validate($data['name'], "required", false));
        $errors->append('email', $this->validate($data['email'], "email", false));
        $errors->append('password', $this->validate($data['password'], "min length:8,max length:255", false));

        return $errors;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @throws Exception
     */
    protected function create(array $data)
    {
        User::create([
            'username' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
