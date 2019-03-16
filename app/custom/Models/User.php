<?php
namespace app\custom\Models;

use app\framework\Component\Database\DB;
use app\framework\Component\Database\Model\Model;
use app\framework\Component\StdLib\Exception\Exception;

class User extends Model
{
    protected $table = "users";

    /**
     * Create new user entry
     * TODO: redo this as soon as the new QueryBuilder gets implemented
     * @param array $data
     * @throws Exception
     */
    public static function create(array $data)
    {
        $userModel   = new User();
        $query       = "INSERT INTO $userModel->table ";
        $columnNames = "(";
        $values      = "(";

        $length = count($data);
        $i      = 1;
        foreach ($data as $columnName => $value) {
            $toPrepend = $length <= $i ? "" : ", ";

            $columnNames .= $columnName . $toPrepend;
            $values      .= "'".$value."'" . $toPrepend;

            $i++;
        }
        $query .= $columnNames .") VALUES ". $values .")";

        if (!DB::insert($query)) {
            throw new Exception("Could not insert entry to database table: ".$userModel->table, $query);
        }
    }
}
