<?php
namespace app\custom\Http\Controller;

use app\custom\Models\User;
use app\framework\Component\VarDumper\VarDumper;

class MainController
{
    public function index()
    {
        VarDumper::dump(User::find(1)->roles()->get());

//        $role = Role::addTable("users_roles")->where("roles.id", "=", "users_roles.roles_id")
//            ->andWhere("users_roles.users_id", "=", 1)
//            ->get(['title', 'power']);
//
//        dd($role);
    }
}
