<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Validation;

trait ValidationTrait
{
    /**
     * Get Validation component
     *
     * @param mixed        $data
     * @param string|array $validators
     * @param bool|true    $throw
     *
     * @internal done like this for backwards compatibility might be changed later on
     * @return Validation|bool
     */
    protected static function validate($data = null, $validators = null, $throw = true)
    {
        if (isset($data) && isset($validators)) {
            return Validation::getInstance()->validate($data, $validators, $throw);
        }

        return Validation::getInstance();
    }
}
