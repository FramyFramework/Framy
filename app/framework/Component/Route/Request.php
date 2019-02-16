<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Route;

use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;

/**
 * Class Request
 * @package app\framework\Component\Route
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
        $this->server       = new ArrayObject($server);
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
}
