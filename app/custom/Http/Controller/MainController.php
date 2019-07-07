<?php
namespace app\custom\Http\Controller;

use app\custom\Models\Comment;
use app\custom\Models\Post;
use app\custom\Models\User;

class MainController
{
    public function index()
    {
        dd(User::find(1)->roles()->get());

        /** @var User $user */
        $post = Post::find(1);
        dd($post->comments());
    }
}
