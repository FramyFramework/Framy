<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Routing;


use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;

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
     * @type ArrayObject
     */
    protected $headers;

    /**
     * HTTP response cookies
     *
     * @type ArrayObject
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

    /**
     * Append a string to the response's content body
     *
     * @param string $content   The string to append
     * @return $this
     */
    public function append($content)
    {
        // Require that the response be unlocked before changing it
        $this->requireUnlocked();

        $this->body .= $content;

        return $this;
    }

    /**
     * Check if the response is locked
     *
     * @return boolean
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * Require that the response is unlocked
     *
     * Throws an exception if the response is locked,
     * preventing any methods from mutating the response
     * when its locked
     *
     * @throws LockedResponseException  If the response is locked
     * @return $this
     */
    public function requireUnlocked()
    {
        if ($this->isLocked()) {
            throw new LockedResponseException('Response is locked');
        }

        return $this;
    }

    /**
     * Lock the response from further modification
     *
     * @return $this
     */
    public function lock()
    {
        $this->locked = true;

        return $this;
    }

    /**
     * Unlock the response from further modification
     *
     * @return $this
     */
    public function unlock()
    {
        $this->locked = false;

        return $this;
    }
}
