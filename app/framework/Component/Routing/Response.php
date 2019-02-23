<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Routing;


class Response
{
    /**
     * The default response HTTP status code
     * @var int
     */
    const DEFAULT_STATUS_CODE = 200;

    /**
     * The HTTP version of the response
     *
     * @type string
     */
    protected $protocol_version = '1.1';

    /**
     * The response body
     *
     * @type string
     */
    protected $body;

    /**
     * HTTP response status
     *
     * @type HttpStatus
     */
    protected $status;

    /**
     * HTTP response headers
     *
     * @type HeaderDataCollection
     */
    protected $headers;

    /**
     * HTTP response cookies
     *
     * @type ResponseCookieDataCollection
     */
    protected $cookies;

    /**
     * Whether or not the response is "locked" from
     * any further modification
     *
     * @type boolean
     */
    protected $locked = false;

    /**
     * Whether or not the response has been sent
     *
     * @type boolean
     */
    protected $sent = false;

    /**
     * Whether the response has been chunked or not
     *
     * @type boolean
     */
    public $chunked = false;
}
