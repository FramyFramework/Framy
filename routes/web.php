<?php
/*-------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Framy the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use app\framework\Component\Routing\Router;

Router::get("/", function() {
    view("landingPage");
});

Router::get("/home", function() {
    view("home");
});

Router::auth();

Router::get("/manager", "ProjectManagerController@dashboard");

Router::get("/manager/wiki", "ProjectManager\WikiController@index");

Router::get("/manager/project/new", "projectManager/project/new");

Router::post("/manager/project/new", "ProjectManager\ProjectController@create");

Router::post("/manager/project/[i:id]/addUser", "ProjectManager\ProjectController@addUser");

Router::get("/manager/project/[i:id]", "ProjectManager\ProjectController@show");

Router::get("/manager/project/[i:id]/settings", "ProjectManager\ProjectController@settings");

Router::post("/manager/project/[i:id]/edit", "ProjectManager\ProjectController@edit");

Router::get("/manager/project/[i:id]/task/new", "ProjectManager\TaskController@showCreate");

Router::post("/manager/project/[i:id]/task/new","ProjectManager\TaskController@create");

Router::get("/manager/project/[i:id]/task/[i:task_id]", "ProjectManager\TaskController@show");

Router::get("/manager/project/[i:id]/task/[i:task_id]/edit", "ProjectManager\TaskController@showEdit");

Router::post("/manager/project/[i:id]/task/[i:task_id]/edit", "ProjectManager\TaskController@edit");

// add more routes here ...
