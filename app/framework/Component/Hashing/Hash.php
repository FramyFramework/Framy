<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Hashing;

/**
 * Hash Facade
 * @package app\framework\Component\Hashing
 */
class Hash
{
    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public static function make($value, array $options = [])
    {
        return static::getHashManager()->make($value, $options);
    }

    /**
     * Get information about the given hashed value.
     *
     * @param  string  $hashedValue
     * @return array
     */
    public static function info($hashedValue)
    {
        return static::getHashManager()->info($hashedValue);
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public static function check($value, $hashedValue, array $options = [])
    {
        return static::getHashManager()->check($value, $hashedValue, $options);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public static function needsRehash($hashedValue, array $options = [])
    {
        return static::getHashManager()->needsRehash($hashedValue, $options);
    }

    private static function getHashManager()
    {
        return new HashManager();
    }
}
