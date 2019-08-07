<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\framework\Component\VarDumper\Caster;

use ProxyManager\Proxy\ProxyInterface;
use app\framework\Component\VarDumper\Cloner\Stub;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ProxyManagerCaster
{
    public static function castProxy(ProxyInterface $c, array $a, Stub $stub, $isNested)
    {
        if ($parent = get_parent_class($c)) {
            $stub->class .= ' - '.$parent;
        }
        $stub->class .= '@proxy';

        return $a;
    }
}
