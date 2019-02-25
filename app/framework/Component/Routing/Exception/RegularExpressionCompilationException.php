<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Routing\Exception;

use RuntimeException;

class RegularExpressionCompilationException extends RuntimeException implements RoutingExceptionInterface
{
}
