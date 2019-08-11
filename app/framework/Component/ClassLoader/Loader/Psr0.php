<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\ClassLoader\Loader;

use app\framework\Component\StdLib\SingletonTrait;

require __DIR__ . "/AbstractLoader.php";
require_once realpath(__DIR__."/../../StdLib/SingletonTrait.php");

/**
 * Psr0 autoloader
 *
 * @package app\framework\Component\ClassLoader\Loader
 */
class Psr0 extends AbstractLoader
{
    use SingletonTrait;

    public function findClass($class)
    {
        $className = ltrim($class, '\\');
        $fileName  = '';
        if ($this->getLastNamespacePosition($className)) {
            $namespace = $this->getNamespace($className);
            $className = $this->getClass($className);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        $path = ROOT_PATH.DIRECTORY_SEPARATOR.$fileName;

        if(file_exists($path)) {
            return $path;
        }

        return false;
    }
}
