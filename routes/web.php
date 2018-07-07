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

    $klein->get("/", function(){
        echo "<pre>";

        $DB = new app\framework\Component\Database\Manager();
        $DB->addDefaultConn();
        $DB->useConnection();
        $data = $DB->getConnection()->select("select * from user where id=1");
        print_r($data);
        $data = $DB->getConnection()->insert("insert into user (name) values ('another one')");
        print_r($data);

        echo "</pre>";
    });

    // add more routes here ...

    $klein->dispatch();