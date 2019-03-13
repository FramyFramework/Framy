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
    view("welcome", ['version' => version()]);
});

$klein->get("/login", function() {
    app("Auth\AuthController@getLogin");
});

$klein->post("/login", function($request) {
    app("Auth\AuthController@postLogin", [$request]);
});

$klein->get("/login_check", function($request) {
    app("Auth\AuthController@check", [$request]);
});

$klein->get("/logout", function($request) {
    app("Auth\AuthController@logout", [$request]);
});

$klein->get("/register", function() {
    app("Auth\AuthController@getRegister");
});

$klein->post("/register", function($request) {
    app("Auth\AuthController@postRegister", [$request]);
});

$klein->get("/check-email", function($request) {
});

$klein->get("/confirm/[:token]", function($request) {
});


$klein->get("/confirmed", function($request) {
});

$klein->get("/change-password", function() {
    app("Auth\AuthController@register");
});

// add more routes here ...

$klein->dispatch();
