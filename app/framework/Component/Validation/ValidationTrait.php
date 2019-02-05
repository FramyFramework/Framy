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
        * @return bool
        */
        protected static function validate($data, $validators, $throw = true)
        {
            return Validation::getInstance()->validate($data, $validators, $throw);
        }
    }
