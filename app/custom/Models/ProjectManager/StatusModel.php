<?php
namespace app\custom\Models\ProjectManager;

use app\framework\Component\Database\DB;
use app\framework\Component\Database\Model\Model;
use app\framework\Component\StdLib\SingletonTrait;

class StatusModel extends Model
{
    use SingletonTrait;

    public function all()
    {
        return DB::select("SELECT * FROM status");
    }

    public function getOneById($id)
    {
        return DB::select("SELECT * FROM status where id=:id", ['id'=>$id])[0];
    }
}
