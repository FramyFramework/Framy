<?php
/**
 * Klein (klein.php) - A fast & flexible router for PHP
 *
 * @author      Chris O'Hara <cohara87@gmail.com>
 * @author      Trevor Suarez (Rican7) (contributor and v2 refactorer)
 * @copyright   (c) Chris O'Hara
 * @link        https://github.com/klein/klein.php
 * @license     MIT
 */

namespace app\framework\Component\Routing\Exceptions;

use RuntimeException;

/**
 * ResponseAlreadySentException
 *
 * Exception used for when a response is attempted to be sent after its already been sent
 * @package app\framework\Component\Routing\Exceptions
 */
class ResponseAlreadySentException extends RuntimeException implements RoutingExceptionInterface
{
}
