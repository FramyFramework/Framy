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

use OverflowException;

/**
 * DuplicateServiceException
 *
 * Exception used for when a service is attempted to be registered that already exists
 * @package app\framework\Component\Routing\Exceptions
 */
class DuplicateServiceException extends OverflowException implements RoutingExceptionInterface
{
}