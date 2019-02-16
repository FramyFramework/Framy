<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Route;

/**
 * Class RouteFactory
 * @package app\framework\Component\Route
 */
class RouteFactory
{
    /**
     * The namespace of which to collect the routes in
     * when matching, so you can define routes under a
     * common endpoint
     *
     * @var string
     */
    protected $namespace;

    /**
     * The value given to path's when they are entered as null values
     *
     * @type string
     */
    const NULL_PATH_VALUE = '*';

    /**
     * Constructor
     *
     * @param string $namespace The initial namespace to set
     */
    public function __construct($namespace = null)
    {
        $this->namespace = $namespace;
    }

    /**
     * Gets the value of namespace
     *
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * Sets the value of namespace
     *
     * @param string $namespace The namespace from which to collect the Routes under
     * @return $this
     */
    public function setNamespace(?string $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Append a namespace to the current namespace
     *
     * @param string $namespace The namespace from which to collect the Routes under
     * @return $this
     */
    public function appendNamespace(string $namespace)
    {
        $this->namespace .= $namespace;

        return $this;
    }

    /**
     * Build factory method
     *
     * This method should be implemented to return a Route instance
     *
     * @param callable      $callback       Callable callback method to execute on route match
     * @param string        $path           Route URI path to match
     * @param string|array  $method  HTTP   Method to match
     * @param boolean       $count_match    Whether or not to count the route as a match when counting total matches
     * @param string        $name           The name of the route
     * @return Route
     */
    public function build($callback, $path = null, $method = null, $count_match = true, $name = null)
    {
        return new Route(
        );
    }
}
