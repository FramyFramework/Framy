<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Routing\Exception;

use RuntimeException;

/**
 * Class HttpException
 * @package app\framework\Component\Routing\Exception
 */
class HttpException extends RuntimeException implements HttpExceptionInterface
{
    /**
     * Create an HTTP exception from nothing but an HTTP code
     *
     * @param int $code
     * @return HttpException
     */
    public static function createFromCode($code)
    {
        return new static(null, (int) $code);
    }
}
