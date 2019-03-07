<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\StdLib\StdObject\ArrayObject;

use app\framework\Component\StdLib\StdObject\StdObjectWrapper;
use app\framework\Component\StdLib\StdObject\StringObject\StringObject;

trait ManipulationTrait
{
    /**
     * remove first element of given array
     *
     * @return $this
     */
    public function removeFirst()
    {
        $array = $this->val();

        array_shift($array);

        $this->val($array);

        return $this;
    }

    /**
     * remove last element of given array
     *
     * @return $this
     */
    public function removeLast()
    {
        $array = $this->val();

        array_pop($array);

        $this->val($array);

        return $this;
    }

    /**
     * The method joins the items in a collection.
     *
     * @param string $glue The string you wish to place between the values.
     * @param string $key  If the value contains arrays or objects, you should pass the key of the attributes you wish to join
     * @return string
     */
    public function implode(string $glue, string $key = null)
    {

        if (! function_exists(__NAMESPACE__.'\useGlue')) {
            // done this function to avoid writing redundant code
            function useGlue(&$i, &$length, &$glue) {
                // don't use glue if is last element
                if($i < $length)
                    return $glue;
                else
                    return "";
            }
        }

        $val    = $this->val();
        $length = $this->count();
        $result = "";

        // check if val is key or obj
        $i = 1;
        foreach ($val as $item) {
            if(is_object($item)) {
                $item = get_object_vars($this);
                //TODO finnish this case
            } elseif(is_array($item)) {
                if(!isset($key))
                    handle(new \Exception("\$key must be set!"));

                $result .= $item[$key].useGlue($i, $length, $glue);
            } else {
                $result .= $item.useGlue($i, $length, $glue);
            }
            $i++;
        }

        return $result;
    }

    /**
     * Get or update the given key inside current array.
     *
     * @param string|int|StringObject $key Array key
     * @param null|mixed              $value If set, the value under current $key will be updated and not returned.
     * @param bool                    $setOnlyIfDoesntExist Set the $value only in case if the $key doesn't exist.
     *
     * @return $this|mixed|StringObject The value of the given key.
     */
    public function key($key, $value = null, $setOnlyIfDoesntExist = false)
    {
        $key = StdObjectWrapper::toString($key);
        $array = $this->val();

        if ($setOnlyIfDoesntExist && !$this->keyExists($key)) {
            $array[$key] = $value;
            $this->val($array);

            return $value;
        } else {
            if (!$setOnlyIfDoesntExist && !$this->isNull($value)) {
                $array[$key] = $value;
                $this->val($array);

                return $this;
            }
        }

        if (!isset($array[$key])) {
            return $value;
        }

        return $array[$key];
    }

    /**
     * Inserts an element to the end of the array.
     * If you set both params, that first param is the key, and second is the value,
     * else first param is the value, and the second is ignored.
     *
     * @param mixed $k
     * @param mixed $v
     *
     * @return $this
     */
    public function append($k, $v = null)
    {
        $array = $this->val();

        if (!$this->isNull($v)) {
            $array[$k] = $v;
        } else {
            $array[] = $k;
        }

        $this->val($array);

        return $this;
    }

    /**
     * Inserts an element at the beginning of the array.
     * If you set both params, that first param is the key, and second is the value,
     * else first param is the value, and the second is ignored.
     *
     * @param mixed $k
     * @param mixed $v
     *
     * @return $this
     */
    public function prepend($k, $v = null)
    {
        $array = $this->val();

        if (!$this->isNull($v)) {
            $array = array_reverse($array, true);
            $array[$k] = $v;
            $array = array_reverse($array, true);
        } else {
            array_unshift($array, $k);
        }

        $this->val($array);

        return $this;
    }

    /**
     * remove key in current array
     *
     * @param $key
     */
    public function removeKey($key)
    {
        if ($this->keyExists($key)) {
            $array = $this->val();
            unset($array[$key]);

            $this->val($array);
        }
    }

    /**
     * The method iterates through the array and passes each value to
     * the given callback. The callback is free to modify the item and return it,
     * thus forming a new ArrayObject of modified items
     *
     * @param callable $call
     * @return ArrayObject
     */
    public function map(callable $call)
    {
        $array  = $this->val();
        $result = [];

        foreach ($array as $key => $item)
            $result[] = call_user_func($call, $item, $key);

        return new ArrayObject($result);
    }

    /**
     * Merge given $array with current array.
     *
     * @param array|ArrayObject $array
     *
     * @return $this
     */
    public function merge($array)
    {
        if($this->isInstanceOf($array, $this)){
            $array = $array->val();
        }

        $this->val(array_merge($this->val(), $array));

        return $this;
    }

    /**
     * Merge given $array with current array.
     *
     * @param array|ArrayObject $array
     *
     * @return $this
     */
    public function mergeRecursive($array)
    {
        if($this->isInstanceOf($array, $this)){
            $array = $array->val();
        }

        $this->val(array_merge_recursive($this->val(), $array));

        return $this;
    }

    /**
     * @param int $num
     *
     * @return ArrayObject
     * @throws ArrayObjectException
     */
    public function rand($num = 1)
    {
        try {
            $arr = array_rand($this->val(), $num);
        } catch (\ErrorException $e) {
            handle(new ArrayObjectException($e->getMessage()));
        }

        if (!$this->isArray($arr)) {
            $arr = [$arr];
        }

        return new ArrayObject($arr);
    }

    /**
     * reverse elements order
     *
     * @param bool $preserve
     *
     * @return $this
     */
    public function reverse($preserve = false)
    {
        $this->val(array_reverse($this->val(), $preserve));

        return $this;
    }

    /**
     * Shuffle elements in the array.
     *
     * @return $this
     */
    public function shuffle()
    {
        $val = $this->val();
        shuffle($val);
        $this->val($val);
        return $this;
    }

    /**
     * Removes duplicate values from an array
     *
     * @param int $sortFlag  Sorting type flags:<br>
     *                       SORT_REGULAR - compare items normally (don't change types)<br>
     *                       SORT_NUMERIC - compare items numerically<br>
     *                       SORT_STRING - compare items as strings<br>
     *                       SORT_LOCALE_STRING - compare items as strings, based on the current locale.<br>
     *
     * @return $this
     */
    public function unique($sortFlag = SORT_REGULAR)
    {
        $this->val(array_unique($this->val(), $sortFlag));

        return $this;
    }

    /**
     * Sort an array by values using a user-defined comparison function<br />
     * This function assigns new keys to the elements in array. It will remove any existing keys that may have been assigned, rather than just reordering the keys.<br />
     * The comparison function must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second.
     *
     * @param callable $comparisonFunction
     *
     * @throws ArrayObjectException
     *
     * @return $this
     */
    public function sortUsingFunction($comparisonFunction)
    {
        if (!is_callable($comparisonFunction)) {
            throw new ArrayObjectException(ArrayObjectException::MSG_INVALID_ARG, [
                '$comparisonFunction',
                'callable'
            ]);
        }

        $val = $this->val();
        usort($val, $comparisonFunction);
        $this->val($val);

        return $this;
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array $array
     * @param  int   $depth
     * @return array
     */
    public function flatten(array $array = null, $depth = INF)
    {
        $result = [];
        $val    = $array ?: $this->val();

        foreach ($val as $item) {
            $item = $item instanceof $this ? $item->val() : $item;

            if (is_array($item)) {
                if ($depth === 1) {
                    $result = array_merge($result, $item);
                    continue;
                }

                $result = array_merge($result, $this->flatten($item, $depth - 1));
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }
}
