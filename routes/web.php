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

    $klein = new Klein();

    $klein->respond("GET", "/", function(){
        $DB = new app\framework\Component\Database\Manager();
        $DB->useDefaultConn();
        $DB->table('user')->run("SELECT * FROM `user`");

        $User = new app\custom\Models\User();

        echo "<pre>";
        $User->name = "test";
        print_r($User);
        echo "</pre>";
    });

    // add more routes here ...

    $klein->dispatch();