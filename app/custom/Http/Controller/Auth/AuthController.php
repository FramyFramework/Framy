<?php

namespace app\custom\Http\Controller\Auth;

use app\framework\Component\Auth\AuthenticatesAndRegistersUsers;

class AuthController
{
   use AuthenticatesAndRegistersUsers;

   public function validator($data)
   {
       $errors = [];

       $errors[] = $this->validate($data['email'], "email", false);
       $errors[] = $this->validate($data['password'], "min length:8,max length:255", false);

       return $errors;
   }
}
