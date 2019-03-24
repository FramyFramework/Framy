<?php

namespace app\custom\Http\Controller\ProjectManager;

use app\custom\Models\ProjectManager\PriorityModel;
use app\custom\Models\ProjectManager\StatusModel;
use app\custom\Models\ProjectManager\TaskModel;
use app\framework\Component\Database\DB;
use app\framework\Component\Validation\ValidationTrait;

class TaskController
{
    use ValidationTrait;

    public function showCreate($projectId)
    {
        StatusModel::getInstance()->all();

        view("projectManager/task/new", [
            'status'     => StatusModel::getInstance()->all(),
            'priorities' => PriorityModel::getInstance()->all(),
            'project' => DB::select("SELECT id FROM projects WHERE id=".$projectId)[0]
        ]);
    }

    function create($projectId)
    {
        // validate fields
        $this->validate($_POST['title'], ['required', 'min length:3', 'max length:255']);
        $this->validate($_POST['description'],['max length:255']);
        $this->validate($_POST['status'],['number']);
        $this->validate($_POST['priority'],['number']);

        TaskModel::getInstance()->create(
            $projectId,
            $_POST['title'],
            $_POST['description'],
            $_POST['status'],
            $_POST['priority']
        );

        header("Location: /manager/project/$projectId");exit;
    }

    function show($projectId, $taskId)
    {
        $task = TaskModel::getInstance()->getOne($projectId, $taskId);

        view("projectManager/task/show", [
            'task' => $task,
            'project' => DB::select("SELECT id FROM projects WHERE id=".$projectId)[0]
        ]);
    }

    function showEdit($projectId, $taskId)
    {
        view("projectManager/task/new", [
            'task'       => TaskModel::getInstance()->getOne($projectId, $taskId, false),
            'status'     => StatusModel::getInstance()->all(),
            'priorities' => PriorityModel::getInstance()->all(),
            'project' => DB::select("SELECT id FROM projects WHERE id=".$projectId)[0]
        ]);
    }

    function edit($projectId, $taskId)
    {
        $errorMsg = [];
        $errorMsg['title']       = $this->validate($_POST['title'], ['required', 'min length:3', 'max length:255'], false);
        $errorMsg['description'] = $this->validate($_POST['description'],['max length:255'], false);
        $errorMsg['status']      = $this->validate($_POST['status'],['number'], false);
        $errorMsg['priority']    = $this->validate($_POST['priority'],['number'], false);

        foreach ($errorMsg as $msg) {
            if($msg !== true) {
                return view("projectManager/task/new", [
                    'task' => TaskModel::getInstance()->getOne($projectId, $taskId),
                    'errors' => $errorMsg
                ]);
            }
        }

        DB::update(
            "UPDATE tasks SET 
                title      =:title, 
                description=:description, 
                assignedTo =:assignedTo, 
                status     =:tstatus, 
                priority   =:priority,
                updated_at =NOW()
            WHERE
                id=:id", [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'assignedTo' => (int)$_POST['assignTo'],
            'tstatus' => (int)$_POST['status'],
            'priority' => (int)$_POST['priority'],
            'id' => (int)$taskId
        ]);

        header("Location: /manager/project/$projectId");exit;
    }
}
