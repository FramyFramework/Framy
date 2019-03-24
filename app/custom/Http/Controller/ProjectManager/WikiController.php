<?php
namespace app\custom\Http\Controller\ProjectManager;


class WikiController
{

    public function index(\app\framework\Component\Route\Klein\Request $request)
    {
        $page = $request->paramsGet()->get("page");

        if ($page === null) {
            view("projectManager/wiki/main");
        } else {
            view("projectManager/wiki/$page");
        }
    }
}
