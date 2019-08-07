<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\framework\Component\VarDumper\Tests\Caster;

use PHPUnit\Framework\TestCase;
use app\framework\Component\VarDumper\Caster\GmpCaster;
use app\framework\Component\VarDumper\Cloner\Stub;
use app\framework\Component\VarDumper\Test\VarDumperTestTrait;

class GmpCasterTest extends TestCase
{
    use VarDumperTestTrait;

    /**
     * @requires extension gmp
     */
    public function testCastGmp()
    {
        $gmpString = gmp_init('1234');
        $gmpOctal = gmp_init(010);
        $gmp = gmp_init('01101');
        $gmpDump = <<<EODUMP
array:1 [
  "\\x00~\\x00value" => %s
]
EODUMP;
        $this->assertDumpEquals(sprintf($gmpDump, $gmpString), GmpCaster::castGmp($gmpString, [], new Stub(), false, 0));
        $this->assertDumpEquals(sprintf($gmpDump, $gmpOctal), GmpCaster::castGmp($gmpOctal, [], new Stub(), false, 0));
        $this->assertDumpEquals(sprintf($gmpDump, $gmp), GmpCaster::castGmp($gmp, [], new Stub(), false, 0));

        $dump = <<<EODUMP
GMP {
  value: 577
}
EODUMP;

        $this->assertDumpEquals($dump, $gmp);
    }
}
