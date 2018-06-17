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
        $fac = new \app\framework\Component\Database\Connection\ConnectionFactory();
        $conn = $fac->make("mysql");
        echo "<pre>";
        var_dump($conn);
        echo "</pre>";
    });

    // add more routes here ...

    $klein->dispatch();