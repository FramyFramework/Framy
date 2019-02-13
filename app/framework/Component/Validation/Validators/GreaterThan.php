<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Validation\Validators;

use app\framework\Component\Route\Klein\Exceptions\ValidationException;
use app\framework\Component\Validation\ValidatorInterface;

class GreaterThan implements ValidatorInterface
{
    /**
     * Get validator name, eg: email
     *
     * @return string
     */
    public function getName()
    {
        return "greater than";
    }

    /**
     * Validate given value, using optional parameters and either throw an exception or return a boolean
     *
     * @param mixed     $value
     * @param array     $params
     * @param bool|true $throw
     *
     * @return boolean|string
     */
    public function validate($value, $params = [], $throw = true)
    {
        $cmp = $params[0];
        if($value > $cmp){
            return true;
        }

        $message = "Value must be greater than %s";
        if($throw){
            throw (new ValidationException($message, $cmp));
        }

        return sprintf($message, $cmp);
    }

}
