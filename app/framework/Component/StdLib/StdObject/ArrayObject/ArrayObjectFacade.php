<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\StdLib\StdObject\ArrayObject;

use ArrayAccess;

/**
 * Class ArrayObjectFacade
 *
 * @package app\framework\Component\StdLib\StdObject\ArrayObject
 */
class ArrayObjectFacade
{
    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * The method collapses an array of arrays into a single array:
     *
     * @return array
     */
    public static function collapse()
    {
        return arr(func_get_args())->flatten();
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param $value
     * @return array
     */
    public static function divide($value)
    {
        return [array_keys($value), array_values($value)];
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array   $array
     * @param  string  $prepend
     * @return array
     */
    public static function dot($array, $prepend = '')
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }
        return $results;
    }

    /**
     * Returns the first element of given array
     *
     * @param $array
     * @return mixed
     */
    public static function first($array)
    {
        return arr($array)->first();
    }

    /**
     * Returns the last element of given array
     *
     * @param $array
     * @return mixed
     */
    public static function last($array)
    {
        return arr($array)->last();
    }

    /**
     * Convert the array into a query string.
     *
     * @param  array  $array
     * @return string
     */
    public static function query($array)
    {
        return http_build_query($array, null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * Returns the difference between $attributes and $original
     * If an value is different the element from $arr1 will be returned
     *
     * @param $arr1
     * @param $arr2
     * @return array
     */
    public static function difference($arr1, $arr2)
    {
        $attributes = [];
        foreach ($arr1 as $attKey => $attribute) {
            foreach ($arr2 as $oriKey => $original) {
                if ($attKey == $oriKey) {
                    if ($attribute !== $original) {
                        $attributes[$attKey] = $attribute;
                    }
                }
            }
        }

        return $attributes;
    }
}
