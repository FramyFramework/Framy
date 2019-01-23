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

    use \app\framework\Component\Route\Klein\Klein;
    use \app\framework\Component\Database\DB;


    $klein = new Klein();

    $klein->get("/", function(){
        echo "<pre>";
        $time_start = microtime(true);

        var_dump(
            DB::delete("DELETE FROM user 
                      WHERE id<:id",
                [":id" => 12]
            )
        );

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        echo '<b>Total Execution Time:</b> '.$execution_time.' secs';

        echo "</pre>";
    });

    // add more routes here ...

    $klein->dispatch();