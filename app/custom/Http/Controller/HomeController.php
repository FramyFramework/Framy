<?php

namespace app\custom\Http\Controller;

use app\framework\Component\Database\DB;

class HomeController
{
    function index()
    {
        dd(
            DB::table("table_name")
                ->where("column_1", "=", 1, "or")
                ->where("column_1", "=", 4)
                ->get()
        );
    }
}
