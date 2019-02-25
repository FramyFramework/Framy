<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Routing\Exception;


/**
 * Interface RoutingExceptionInterface
 * Exception interface that the routing exceptions should implement
 *
 * This is mostly for having a simple, common Interface class/namespace
 * that can be type-hinted/instance-checked against, therefore making it
 * easier to handle Klein exceptions while still allowing the different
 * exception classes to properly extend the corresponding SPL Exception type
 *
 * @package app\framework\Component\Routing\Exception
 */
interface RoutingExceptionInterface
{
}
