<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Encryption;


class Crypt
{
    public static function encryptString($value)
    {
        $Encryptor = new \app\framework\Component\Encryption\Encrypter(
            \app\framework\Component\Config\Config::getInstance()->get("CrypKey")
        );

        return $Encryptor->encryptString($value);
    }

    public static function decryptString($payload)
    {
        $Encryptor = new \app\framework\Component\Encryption\Encrypter(
            \app\framework\Component\Config\Config::getInstance()->get("CrypKey")
        );

        return $Encryptor->decryptString($payload);
    }
}