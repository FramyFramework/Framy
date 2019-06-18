<?php
namespace app\custom\Http\Controller;

use app\custom\Models\Comment;

class MainController
{
    public function index()
    {
        $comment = Comment::find(1);

        dd($comment->byUser());
    }
}
