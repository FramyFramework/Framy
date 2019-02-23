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

Router::get("/", "AuthController@index");

Router::getInstance()->dispatch();
