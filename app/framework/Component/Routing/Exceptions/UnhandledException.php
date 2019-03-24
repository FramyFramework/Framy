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
 * UnhandledException
 *
 * Exception used for when a exception isn't correctly handled by the Klein error callbacks
 * @package app\framework\Component\Routing\Exceptions
 */
class UnhandledException extends RuntimeException implements RoutingExceptionInterface
{
}
