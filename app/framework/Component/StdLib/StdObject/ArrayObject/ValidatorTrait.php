<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\StdLib\StdObject\ArrayObject;

use ArrayAccess;

trait ValidatorTrait
{
    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isAccessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Checks if a value exists in an array
     *
     * @param      $value
     * @param bool $strict
     *
     * @return bool
     */
    public function inArray($value, $strict = false)
    {
        return in_array($value, $this->val(), $strict);
    }

    public function keyExists($key)
    {
        return array_key_exists($key, $this->val());
    }

    public function keysExist($keys = [])
    {
        foreach($keys as $key){
            if(!$this->keyExists($key)){
                return false;
            }
        }

        return true;
    }
}
