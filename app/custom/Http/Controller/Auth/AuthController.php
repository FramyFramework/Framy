<?php

namespace app\custom\Http\Controller\Auth;

use app\custom\Models\User;
use app\framework\Component\Auth\AuthenticatesAndRegistersUsers;
use app\framework\Component\Hashing\Hash;
use app\framework\Component\StdLib\Exception\Exception;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;

class AuthController
{
   use AuthenticatesAndRegistersUsers;

   public function validator($data)
   {
       $errors = new ArrayObject([]);

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
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
