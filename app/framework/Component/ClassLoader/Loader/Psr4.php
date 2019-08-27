<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\ClassLoader\Loader;

use app\framework\Component\StdLib\SingletonTrait;

require_once __DIR__ . "/AbstractLoader.php";
require_once realpath(__DIR__."/../../StdLib/SingletonTrait.php");

/**
 * Psr4 autoloader
 *
 * @package app\framework\Component\ClassLoader\Loader\Psr4
 */
class Psr4 extends AbstractLoader
{
    use SingletonTrait;

    /**
     * Returns File path or false
     *
     * @param $name
     *
     * @return mixed
     */
    public function findClass($name)
    {
        $className = ltrim($name, '\\');
        $namespace = $this->getNamespace($className);
        $className = $this->getClass($className);
        $paths     = require(ROOT_PATH."/config/classloader.php");

        $path = array_search($namespace, $paths).DIRECTORY_SEPARATOR.$className.".php";

        if(file_exists($path)) {
            return $path;
        }

        return false;
    }
}
