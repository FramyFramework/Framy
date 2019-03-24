<?php

namespace app\custom\Http\Controller\ProjectManager;

use app\framework\Component\Database\DB;
use app\framework\Component\Route\Klein\Request;
use app\framework\Component\Validation\ValidationTrait;
use http\Header;

class ProjectController
{
    use ValidationTrait;

    function create()
    {
        DB::insert("INSERT INTO projects (
            name, description, owner, created_at, updated_at  
        ) VALUE (
            :pname, 
            :description,
            :powner, 
            NOW(), 
            NOW()
        )", [
            'pname'       => $_POST['name'],
            'description' => $_POST['description'],
            'powner'      => $_SESSION['user']['id']
        ]);

        header("Location: /manager");exit;
    }

    function show($id)
    {
        $project = DB::select(
            "SELECT * FROM projects WHERE id=:id and owner=:powner",
            ['id' => $id, 'powner' => $_SESSION['user']['id']]
        )[0];

        $tasks = DB::select(
            "SELECT * FROM tasks WHERE project=:projectId ORDER BY status, priority DESC",
            ['projectId' => $project->id]
        );

        foreach ($tasks as $key => $task) {
            $tasks[$key]->status = DB::select(
                "SELECT text FROM status WHERE id=:stat",
                ['stat' => $task->status]
            )[0]->text;
            $tasks[$key]->priority = DB::select(
                "SELECT text FROM priority WHERE id=:priority",
                ['priority' => $task->priority]
            )[0]->text;
            $tasks[$key]->createdBy = DB::select(
                "SELECT username FROM user WHERE id=:id",
                ['id' => $task->createdBy]
            )[0]->username;
            $tasks[$key]->assignedTo = DB::select(
                "SELECT username FROM user WHERE id=:id",
                ['id' => $task->assignedTo]
            )[0]->username;
        }

        view("projectManager/project/overview", ['project' => $project, 'tasks'=>$tasks]);
    }

    public function settings(Request $request, $errors = null, $page = null)
    {
        $members = [];
        if ($request->paramsGet()->get("p") == "members") {
            // get id of project owner
            $ownerID = DB::select("SELECT owner FROM projects WHERE id=:id", [
                'id' => $request->__get('id')
            ])[0]->owner;

            // use owner id to get owner username
            $member['username'] = DB::select(
                "SELECT username FROM user WHERE id=:id",
                ['id'=>$ownerID]
            )[0]->username;

            // get number of tasks assigned to user.
            $member['tasksAssigned'] = count(DB::select(
                "SELECT id FROM tasks WHERE assignedTo=:user_id", [
                    'id' => $_SESSION['user']['id']
                ]
            ));

            $members[] = $member;
        }
        return view("projectManager/project/settings", [
            'project' => DB::select(
                "SELECT * FROM projects WHERE id=:id",
                ['id' => $request->__get("id")]
            )[0],
            'members' => $members,
            'errors' => $errors,
            'goto' => $page
        ]);
    }

    public function edit(Request $request)
    {
        switch ($request->paramsPost()->get("category")) {
            case "general":
                $param = $request->paramsPost()->all();
                $param['pname'] = $param['name'];
                DB::update(
                    "UPDATE projects SET name=:pname, description=:description WHERE id=".$request->__get("id"),
                    $param
                );
                break;
        }

        header("Location: /manager/project/".$request->__get("id")."/settings");
        exit;
    }

    /**
     * Add user to project
     * @param Request $request
     */
    public function addUser(Request $request)
    {
        $userIdToAdd = $request->paramsPost()->get("userId");
        $errormessage = $this->validate($userIdToAdd, "number,required", false);
        $projectId = $request->__get("id");

        // check if user is already in project
        $owner = DB::select("SELECT owner FROM projects WHERE id=".$projectId)[0]->owner;
        if ($owner == $userIdToAdd) {
            $errormessage = "You cant add the owner to his own project";
        }

        $userId = DB::select("SELECT id FROM user WHERE id=".$userIdToAdd)[0]->id;

        if (!is_null($userId)) {
            $errormessage = "User does not exist";
        }

        if (!$errormessage) {
            return $this->settings($request, $errormessage, "members");
        }

        $userId = DB::select(
            "SELECT user_id FROM `projects-users` WHERE project_id=".$projectId
        )[0]->user_id;

        if (is_null($userId)) {
            DB::insert("INSERT INTO `projects-users` VALUES (
                :user_id,
                :project_id,
                now(),
                now()
             )", [
                "user_id" => (int)$userIdToAdd,
                "project_id" => (int)$projectId
            ]);

            header("Location: /manager/project/$projectId/settings?p=members");
            exit;
        } else {
            $errormessage = "User already in project";
            return $this->settings($request, $errormessage, "members");
        }
    }
}
