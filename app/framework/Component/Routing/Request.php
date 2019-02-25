<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Routing;

use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;

/**
 * Class Request
 * @package app\framework\Component\Routing
 */
class Request
{
    /**
     * Unique identifier for the request
     *
     * @var string
     */
    protected $id;

    /**
     * GET (query) parameters
     *
     * @var ArrayObject
     */
    protected $params_get;

    /**
     * POST parameters
     *
     * @var ArrayObject
     */
    protected $params_post;

    /**
     * Named parameters
     *
     * @var ArrayObject
     */
    protected $params_named;

    /**
     * Client cookie data
     *
     * @var ArrayObject
     */
    protected $cookies;

    /**
     * Server created attributes
     *
     * @var ArrayObject
     */
    protected $server;

    /**
     * HTTP request headers
     *
     * @var ArrayObject
     */
    protected $headers;

    /**
     * Uploaded temporary files
     *
     * @var ArrayObject
     */
    protected $files;

    /**
     * The request body
     *
     * @var string
     */
    protected $body;

    /**
     * Request constructor.
     *
     * @param array $params_get
     * @param array $params_post
     * @param array $cookies
     * @param array $server
     * @param array $files
     * @param null $body
     */
    public function __construct(
        array $params_get  = [],
        array $params_post = [],
        array $cookies     = [],
        array $server      = [],
        array $files       = [],
              $body        = null
    ) {
        $this->params_get   = new ArrayObject($params_get);
        $this->params_post  = new ArrayObject($params_post);
        $this->cookies      = new ArrayObject($cookies);
        $this->server       = new ServerArrayObject($server);
        $this->headers      = new ArrayObject($this->server->getHeaders());
        $this->files        = new ArrayObject($files);
        $this->body         = $body ? (string) $body : null;
    }

    /**
     * Create a new request object using the built-in "superglobals"
     *
     * @link http://php.net/manual/en/language.variables.superglobals.php
     * @return Request
     */
    public static function createFromGlobals()
    {
        // Create and return a new instance of this
        return new static(
            $_GET,
            $_POST,
            $_COOKIE,
            $_SERVER,
            $_FILES,
            null // Let our content getter take care of the "body"
        );
    }

    /**
     * Gets the request URI
     *
     * @return string
     */
    public function uri()
    {
        return $this->server->offsetGet('REQUEST_URI') ?: '/';
    }

    /**
     * Get the request's pathname
     *
     * @return string
     */
    public function pathname()
    {
        $uri = $this->uri();

        // Strip the query string from the URI
        $uri = strstr($uri, '?', true) ?: $uri;

        return $uri;
    }

    /**
     * Gets the request method, or checks it against $is
     *
     * <code>
     * // POST request example
     * $request->method() // returns 'POST'
     * $request->method('post') // returns true
     * $request->method('get') // returns false
     * </code>
     *
     * @param string $is				The method to check the current request method against
     * @param boolean $allow_override	Whether or not to allow HTTP method overriding via header or params
     * @return string|boolean
     */
    public function method(string $is = null, bool $allow_override = true)
    {
        $method = $this->server->offsetGet('REQUEST_METHOD') ?: 'GET';

        // Override
        if ($allow_override && $method === 'POST') {
            // For legacy servers, override the HTTP method with the X-HTTP-Method-Override header or _method parameter
            if ($this->server->keyExists('X_HTTP_METHOD_OVERRIDE')) {
                $method = $this->server->offsetGet('X_HTTP_METHOD_OVERRIDE') ?: $method;
            } else {
                $method = $this->param('_method', $method);
            }

            $method = strtoupper($method);
        }

        // We're doing a check
        if (null !== $is) {
            return strcasecmp($method, $is) === 0;
        }

        return $method;
    }

    /**
     * Returns all parameters (GET, POST, named, and cookies) that match the mask
     *
     * Takes an optional mask param that contains the names of any params
     * you'd like this method to exclude in the returned array
     *
     * @see \app\framework\Component\Routing\Klein\DataCollection\DataCollection::all()
     * @param array $mask               The parameter mask array
     * @param boolean $fill_with_nulls  Whether or not to fill the returned array
     *  with null values to match the given mask
     * @return array
     */
    public function params($mask = null, $fill_with_nulls = true)
    {
        /*
         * Make sure that each key in the mask has at least a
         * null value, since the user will expect the key to exist
         */
        if (null !== $mask && $fill_with_nulls) {
            $attributes = array_fill_keys($mask, null);
        } else {
            $attributes = array();
        }

        // Merge our params in the get, post, cookies, named order
        return array_merge(
            $attributes,
            $this->params_get->val($mask),
            $this->params_post->val($mask),
            $this->cookies->val($mask),
            $this->params_named->val($mask) // Add our named params last
        );
    }

    /**
     * Return a request parameter, or $default if it doesn't exist
     *
     * @param string $key       The name of the parameter to return
     * @param mixed $default    The default value of the parameter if it contains no value
     * @return mixed
     */
    public function param($key, $default = null)
    {
        // Get all of our request params
        $params = $this->params();

        return isset($params[$key]) ? $params[$key] : $default;
    }
}
