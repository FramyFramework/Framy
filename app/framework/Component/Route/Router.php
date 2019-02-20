<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Route;

use app\framework\Component\StdLib\SingletonTrait;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;

/**
 * Class Router
 * @package app\framework\Component\Route
 */
class Router
{
    use SingletonTrait;

    /**
     * The regular expression used to compile and match URL's
     *
     * @type string
     */
    const ROUTE_COMPILE_REGEX = '`(\\\?(?:/|\.|))(?:\[([^:\]]*)(?::([^:\]]*))?\])(\?|)`';

    /**
     * The regular expression used to escape the non-named param section of a route URL
     *
     * @type string
     */
    const ROUTE_ESCAPE_REGEX = '`(?<=^|\])[^\]\[\?]+?(?=\[|$)`';

    /**
     * The types to detect in a defined match "block"
     *
     * Examples of these blocks are as follows:
     *
     * - integer:       '[i:id]'
     * - alphanumeric:  '[a:username]'
     * - hexadecimal:   '[h:color]'
     * - slug:          '[s:article]'
     *
     * @type array
     */
    protected $match_types = array(
        'i'  => '[0-9]++',
        'a'  => '[0-9A-Za-z]++',
        'h'  => '[0-9A-Fa-f]++',
        's'  => '[0-9A-Za-z-_]++',
        '*'  => '.+?',
        '**' => '.++',
        ''   => '[^/]+?'
    );

    /**
     * Collection of the routes to match on dispatch
     *
     * @type ArrayObject
     */
    protected $routes;

    /**
     * The Route factory object responsible for creating Route instances
     *
     * @type RouteFactory
     */
    protected $route_factory;

    /**
     * The Request object passed to each matched route
     *
     * @type Request
     */
    protected $request;

    /**
     * The Response object passed to each matched route
     *
     * @type Response
     */
    protected $response;

    /**
     * The service provider object passed to each matched route
     *
     * @type ServiceProvider
     */
    protected $service;

    public function init(ArrayObject $routes = null, RouteFactory $routeFactory = null)
    {
        $this->routes        = $routes       ?: new ArrayObject([]);
        $this->route_factory = $routeFactory ?: new RouteFactory();
    }

    /**
     * @param string|array $method      HTTP Method to match
     * @param string       $path        Route URI path to match
     * @param callable     $callback    Callable callback method to execute on route match
     * @return Route
     */
    public function respond($method, $path = '*', $callback = null)
    {
        $route = $this->route_factory->build($callback, $path, $method);

        $this->routes->append($route);

        return $route;
    }

    public function dispatch(
        Request  $request       = null,
        Response $response      = null,
                 $send_response = true
    ) {
        // Set/Initialize our objects to be sent in each callback
        $this->request  = $request  ?: Request::createFromGlobals();
        $this->response = $response ?: new Response();

        // Grab some data from the request
        $uri        = $this->request->pathname();
        $req_method = $this->request->method();
    }

    /**
     * GET alias for "respond()"
     *
     * @param string    $path       Route URI path to match
     * @param callable  $callback   Callable callback method to execute on route match
     * @return Route
     */
    public static function get($path = '*', $callback = null)
    {
        return Router::getInstance()->respond("GET", $path, $callback);
    }
}
