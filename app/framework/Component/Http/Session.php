<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Http;

/**
 * Class Session
 * Simple class for interacting with the Session
 *
 * @package app\framework\Component\Http
 */
class Session implements SessionInterface
{
    /**
     * @inheritDoc
     */
    public function start()
    {
        if ($this->isStarted()) {
            session_start();
        }
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        session_id($id);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        // TODO: Implement setName() method.
    }

    /**
     * @inheritDoc
     */
    public function invalidate($lifetime = null)
    {
        // TODO: Implement invalidate() method.
    }

    /**
     * @inheritDoc
     */
    public function migrate($destroy = false, $lifetime = null)
    {
        // TODO: Implement migrate() method.
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        // TODO: Implement save() method.
    }

    /**
     * @inheritDoc
     */
    public function has($name)
    {
        // TODO: Implement has() method.
    }

    /**
     * @inheritDoc
     */
    public function get($name, $default = null)
    {
        return $_SESSION[$name] ?: $default;
    }

    /**
     * @inheritDoc
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $_SESSION;
    }

    /**
     * @inheritDoc
     */
    public function replace(array $attributes)
    {
        // TODO: Implement replace() method.
    }

    /**
     * @inheritDoc
     */
    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        session_unset();
    }

    /**
     * @inheritDoc
     */
    public function isStarted()
    {
        return session_status() === PHP_SESSION_NONE;
    }
}
