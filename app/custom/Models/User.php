<?php
namespace app\custom\Models;

use app\framework\Component\Database\Model\Model;
use app\framework\Component\StdLib\Exception\Exception;

class User extends Model
{
    protected $table = "users";

    /**
     * Create new user entry
     *
     * @param array $data
     * @throws Exception
     */
    public static function create(array $data)
    {
        $user = new User();

        foreach ($data as $columnName => $value) {
            $user->$columnName = $value;
        }

        $user->save();
    }

    public function comments()
    {
        return $this->belongsTo(Comment::class, 'id', 'byUser');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
