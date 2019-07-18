<?php
namespace app\custom\Http\Controller;

use app\custom\Models\Comment;
use app\custom\Models\Post;
use app\custom\Models\Role;
use app\custom\Models\User;

class MainController
{
    public function index()
    {
//        dd(User::find(1)->roles()->get());

        $role = Role::where("role.id", "=", "role_user.role_id")
            ->andWhere("role_user.user_id", "=", 1)
            ->get(['title', 'power']);

        dd($role);

        // select
        //     *
        // from
        //     user, role, role_user
        // where
        //     role.id = role_user.role_id
        //     and
        //     role_user.user_id = user.id

        /** @var User $user */
        $post = Post::find(1);
        dd($post->comments());
    }
}
