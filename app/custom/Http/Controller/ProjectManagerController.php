<?php

namespace app\custom\Http\Controller;

use app\framework\Component\Database\DB;

class ProjectManagerController
{
   public function dashboard()
   {
       $projects = DB::select(
           "SELECT id, name, description FROM projects WHERE owner=:user_id",
           ['user_id' => $_SESSION['user']['id']]
       );

       view("projectManager/dashboard", ['projects' => $projects]);
   }
}