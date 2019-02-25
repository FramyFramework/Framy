<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Routing;

use app\framework\Component\Exception\Handler;
use app\framework\Component\Routing\Exception\HttpException;
use app\framework\Component\Routing\Exception\RegularExpressionCompilationException;
use Throwable;
use app\framework\Component\StdLib\SingletonTrait;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;
use app\framework\Component\Routing\Exception\HttpExceptionInterface;

/**
 * Class Router
 * @package app\framework\Component\Routing
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
        ''   => '[^/]+?',
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

        // Set up some variables for matching
        $skip_num        = 0;
        $matched         = new ArrayObject([]);
        $methods_matched = [];
        $params          = [];
        $apc             = function_exists('apc_fetch');

        try {
            /** @var Route $route */
            foreach ($this->routes->val() as $route) {
                // Are we skipping any matches?
                if ($skip_num > 0) {
                    $skip_num--;
                    continue;
                }

                // Grab the properties of the route handler
                $methods     = $route->getMethods();
                $path        = $route->getUri();
                $count_match = $route->getCountMatch();

                $possible_match = $this->getPossibleMatch($methods, $req_method);

                // ! is used to negate a match
                if (isset($path[0]) && $path[0] === '!') {
                    $negate = true;
                    $i = 1;
                } else {
                    $negate = false;
                    $i = 0;
                }

                // Check for a wildcard (match all)
                if ($path === '*') {
                    $match = true;

                } elseif (($path === '404' && empty($matched->val()) && count($methods_matched) <= 0)
                    || ($path === '405' && empty($matched->val()) && count($methods_matched) > 0)) {

                    // Warn user of deprecation
                    trigger_error(
                        'Use of 404/405 "routes" is deprecated. Use $klein->onHttpError() instead.',
                        E_USER_DEPRECATED
                    );

                    continue;

                } elseif (isset($path[$i]) && $path[$i] === '@') {
                    // @ is used to specify custom regex

                    $match = preg_match('`' . substr($path, $i + 1) . '`', $uri, $params);

                } else {
                    // Compiling and matching regular expressions is relatively
                    // expensive, so try and match by a substring first

                    $expression = null;
                    $regex = false;
                    $j = 0;
                    $n = isset($path[$i]) ? $path[$i] : null;

                    // Find the longest non-regex substring and match it against the URI
                    while (true) {
                        if (!isset($path[$i])) {
                            break;
                        } elseif (false === $regex) {
                            $c = $n;
                            $regex = $c === '[' || $c === '(' || $c === '.';
                            if (false === $regex && false !== isset($path[$i+1])) {
                                $n = $path[$i + 1];
                                $regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
                            }
                            if (false === $regex && $c !== '/' && (!isset($uri[$j]) || $c !== $uri[$j])) {
                                continue 2;
                            }
                            $j++;
                        }
                        $expression .= $path[$i++];
                    }

                    try {
                        // Check if there's a cached regex string
                        if (false !== $apc) {
                            $regex = apc_fetch("route:$expression");
                            if (false === $regex) {
                                $regex = $this->compileRoute($expression);
                                apc_store("route:$expression", $regex);
                            }
                        } else {
                            $regex = $this->compileRoute($expression);
                        }
                    } catch (RegularExpressionCompilationException $e) {
                        throw RoutePathCompilationException::createFromRoute($route, $e);
                    }

                    $match = preg_match($regex, $uri, $params);
                }

                if (isset($match) && $match ^ $negate) {
                    if ($possible_match) {
                        // Handle our response callback
                        try {
                            $this->handleRouteCallback($route, $matched, $methods_matched);

                        } catch (DispatchHaltedException $e) {
                            switch ($e->getCode()) {
                                case DispatchHaltedException::SKIP_THIS:
                                    continue 2;
                                    break;
                                case DispatchHaltedException::SKIP_NEXT:
                                    $skip_num = $e->getNumberOfSkips();
                                    break;
                                case DispatchHaltedException::SKIP_REMAINING:
                                    break 2;
                                default:
                                    throw $e;
                            }
                        }

                        if ($path !== '*') {
                            $count_match && $matched->append($route);
                        }
                    }

                    // Don't bother counting this as a method match if the route isn't supposed to match anyway
                    if ($count_match) {
                        // Keep track of possibly matched methods
                        $methods_matched = array_merge($methods_matched, (array) $methods);
                        $methods_matched = array_filter($methods_matched);
                        $methods_matched = array_unique($methods_matched);
                    }
                }
            }

            // Handle our 404/405 conditions
            if (empty($matched->val()) && count($methods_matched) > 0) {
                // Add our methods to our allow header
                $this->response->header('Allow', implode(', ', $methods_matched));

                if (strcasecmp($req_method, 'OPTIONS') !== 0) {
                    throw HttpException::createFromCode(405);
                }
            } elseif (empty($matched->val())) {
                throw HttpException::createFromCode(404);
            }
        } catch (HttpExceptionInterface $e) {
            // Grab our original response lock state
            $locked = $this->response->isLocked();

            //TODO http error handlers
            print($e->getCode());

            // Make sure we return our response to its original lock state
            if (!$locked) {
                $this->response->unlock();
            }
        } catch (Throwable $e) {handle($e);}
    }

    /**
     * Compiles a route string to a regular expression
     *
     * @param string $route     The route string to compile
     * @return string
     */
    protected function compileRoute($route)
    {
        // First escape all of the non-named param (non [block]s) for regex-chars
        $route = preg_replace_callback(
            static::ROUTE_ESCAPE_REGEX,
            function ($match) {
                return preg_quote($match[0]);
            },
            $route
        );

        // Get a local reference of the match types to pass into our closure
        $match_types = $this->match_types;

        // Now let's actually compile the path
        $route = preg_replace_callback(
            static::ROUTE_COMPILE_REGEX,
            function ($match) use ($match_types) {
                list(, $pre, $type, $param, $optional) = $match;

                if (isset($match_types[$type])) {
                    $type = $match_types[$type];
                }

                // Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                    . ($pre !== '' ? $pre : null)
                    . '('
                    . ($param !== '' ? "?P<$param>" : null)
                    . $type
                    . '))'
                    . ($optional !== '' ? '?' : null);

                return $pattern;
            },
            $route
        );

        $regex = "`^$route$`";

        // Check if our regular expression is valid
        $this->validateRegularExpression($regex);

        return $regex;
    }

    /**
     * Validate a regular expression
     *
     * This simply checks if the regular expression is able to be compiled
     * and converts any warnings or notices in the compilation to an exception
     *
     * @param  string $regex                         The regular expression to validate
     * @throws RegularExpressionCompilationException If the expression can't be compiled
     * @return boolean
     */
    private function validateRegularExpression($regex)
    {
        $error_string = null;

        // Set an error handler temporarily
        set_error_handler(
            function ($errno, $errstr) use (&$error_string) {
                $error_string = $errstr;
            },
            E_NOTICE | E_WARNING
        );

        if (false === preg_match($regex, null) || !empty($error_string)) {
            // Remove our temporary error handler
            restore_error_handler();

            throw new RegularExpressionCompilationException(
                $error_string,
                preg_last_error()
            );
        }

        // Remove our temporary error handler
        restore_error_handler();

        return true;
    }

    /**
     * Handle a route's callback
     *
     * This handles common exceptions and their output
     * to keep the "dispatch()" method DRY
     *
     * @param Route $route
     * @param ArrayObject $matched
     * @param array $methods_matched
     * @return void
     */
    protected function handleRouteCallback(Route $route, ArrayObject $matched, array $methods_matched)
    {
        $callable = $route->getAction();
        $passData = [
            $this->request,
            $this->response,
            $this->service,
            $this, // Pass the Klein instance
            $matched,
            $methods_matched
        ];

        if (is_string($callable)) {
            app($callable, $passData);
        }

        // Handle the callback
        $returned = call_user_func(
            $callable,
            $this->request,
            $this->response,
            $this->service,
            $this, // Pass the Klein instance
            $matched,
            $methods_matched
        );

        if ($returned instanceof AbstractResponse) {
            $this->response = $returned;
        } else {
            // Otherwise, attempt to append the returned data
            try {
                $this->response->append($returned);
            } catch (LockedResponseException $e) {
                // Do nothing, since this is an automated behavior
            }
        }
    }

    private function getPossibleMatch($methods, $req_method)
    {
        // Keep track of whether this specific request method was matched
        $method_match = null;

        // Was a method specified? If so, check it against the current request method
        if (is_array($methods)) {
            foreach ($methods as $test) {
                if (strcasecmp($req_method, $test) === 0) {
                    $method_match = true;
                } elseif (strcasecmp($req_method, 'HEAD') === 0
                    && (strcasecmp($test, 'HEAD') === 0
                        || strcasecmp($test, 'GET') === 0)
                ) {
                    // Test for HEAD request (like GET)
                    $method_match = true;
                }
            }

            if (null === $method_match) {
                $method_match = false;
            }
        } elseif (null !== $methods && strcasecmp($req_method, $methods) !== 0) {
            $method_match = false;

            // Test for HEAD request (like GET)
            if (strcasecmp($req_method, 'HEAD') === 0
                && (strcasecmp($methods, 'HEAD') === 0 || strcasecmp($methods, 'GET') === 0 )) {

                $method_match = true;
            }
        } elseif (null !== $methods && strcasecmp($req_method, $methods) === 0) {
            $method_match = true;
        }

        // If the method was matched or if it wasn't even passed (in the route callback)
        return (null === $method_match) || $method_match;
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
