<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Routing;

/**
 * Class Route
 * @package app\framework\Component\Routing
 */
class Route
{
    /**
     * The URI pattern the route responds to.
     *
     * Examples:
     * - '/posts'
     * - '/posts/[:post_slug]'
     * - '/posts/[i:id]'
     *
     * @var string
     */
    protected $uri;

    /**
     * The HTTP methods the route responds to.
     *
     * May either be represented as a string or an array containing multiple methods to match
     *
     * Examples:
     * - 'POST'
     * - array('GET', 'POST')
     *
     * @var array
     */
    protected $methods;

    /**
     * The route action array.
     *
     * @var callable|string
     */
    protected $action;

    /**
     * Whether or not to count this route as a match when counting total matches
     *
     * @var boolean
     */
    protected $count_match;

    /**
     * The name of the route
     *
     * Mostly used for reverse routing
     *
     * @var string
     */
    protected $name;

    /**
     * Create a new Route instance.
     *
     * @param callable      $callback       Callable callback method to execute on route match
     * @param string        $path           Route URI path to match
     * @param string|array  $method  HTTP   Method to match
     * @param boolean       $count_match    Whether or not to count the route as a match when counting total matches
     * @param string        $name           The name of the route
     * @return void
     */
    public function __construct($callback, $path = null, $method = null, $count_match = true, string $name = "")
    {
        // Initialize some properties (use our setters so we can validate param types)
        $this->setAction($callback);
        $this->setUri($path);
        $this->setMethods($method);
        $this->setCountMatch($count_match);
        $this->setName($name);
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Get the path
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param $methods
     */
    public function setMethods($methods): void
    {
        $this->methods = (array)$methods;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param callable|string $action
     */
    public function setAction($action): void
    {
        $this->action = $this->parseAction($action);
    }

    /**
     * @return callable|string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param bool $count_match
     */
    public function setCountMatch(bool $count_match): void
    {
        $this->count_match = $count_match;
    }

    /**
     * Get the count_match
     *
     * @return boolean
     */
    public function getCountMatch()
    {
        return $this->count_match;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Check if passed controller method is accessible. Callable gets ignored
     *
     * @param callable|string $action
     * @return callable|string
     */
    private function parseAction($action)
    {
        if (! is_callable($action)) {
            if ($GLOBALS['App']->validateClass(explode("@", $action)[0])) {
                return $action;
            }
        }

        return $action;
    }
}
