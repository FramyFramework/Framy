<?php
define('FRAMY_START', microtime(true));
define('ROOT_PATH', realpath(__DIR__."/../"));

session_start();

/*---------------------------------------
| Register Class Loader
|----------------------------------------
|
| The house made Class auto loader.
|
*/
require(ROOT_PATH."/app/framework/Component/ClassLoader/ClassLoader.php");

$autoLoader = new \app\framework\Component\ClassLoader\ClassLoader();
$autoLoader->register();

$App = new \app\framework\Component\App\App();

include("helper.php");
