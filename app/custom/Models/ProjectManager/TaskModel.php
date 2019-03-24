<?php
/**
 * Created by PhpStorm.
 * User: mrfibunacci
 * Date: 08.02.19
 * Time: 06:36
 */

namespace app\custom\Models\ProjectManager;

use app\custom\Models\UserModel;
use app\framework\Component\Database\DB;
use app\framework\Component\Database\Model\Model;
use app\framework\Component\StdLib\SingletonTrait;

class TaskModel extends Model
{
    use SingletonTrait;

    protected $table = 'tasks';

    public function create($projectId, $title, $description, $status, $priority)
    {
        return DB::insert("INSERT INTO tasks (
                project, title, description, createdBy, status, priority, created_at, updated_at
            ) VALUE (
                :project,
                :title,
                :description,
                :creator,
                :status,
                :priority,
                NOW(), 
                NOW()
            )", [
            'project' => (int)$projectId,
            'title' => $title,
            'description' => $description,
            'creator' => $_SESSION['user']['id'],
            'status' => (int)$status,
            'priority' => (int)$priority
        ]);
    }

    public function getOne($projectId, $taskId, $doTheOtherStuff = true)
    {
        $value = DB::select(
            "SELECT * FROM tasks WHERE project=:project_id and id=:task_id",
            ['project_id' => $projectId, 'task_id' => $taskId]
        )[0];

        if ($doTheOtherStuff) {
            $value->createdByUser = UserModel::getInstance()->getUserNameById($value->createdBy);
            $value->status        = StatusModel::getInstance()->getOneById($value->status)->text;
            $value->priority      = PriorityModel::getInstance()->getOneById($value->priority)->text;
        }

        return $value;
    }
}
