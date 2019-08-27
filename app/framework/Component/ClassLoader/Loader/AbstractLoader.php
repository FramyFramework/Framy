<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\ClassLoader\Loader;


use app\framework\Component\StdLib\SingletonTrait;

require_once realpath(__DIR__."/../../StdLib/SingletonTrait.php");

abstract class AbstractLoader
{
    /**
     * Returns File path or false
     *
     * @param $name
     *
     * @return mixed
     */
    abstract public function findClass($name);

    protected function getLastNamespacePosition(string $namespace): int
    {
        return strrpos($namespace, '\\');
    }

    protected function getNamespace(string $className):string
    {
        return substr($className, 0, $this->getLastNamespacePosition($className));
    }

    protected function getClass(string $className):string
    {
        return substr($className, $this->getLastNamespacePosition($className) + 1);
    }
}
