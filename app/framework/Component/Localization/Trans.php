<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Localization;


class Trans
{
    public static function getLocale()
    {
        return TranslationManager::getInstance()->getLocale();
    }

    public static function setLocale($locale)
    {
        TranslationManager::getInstance()->setLocale($locale);
    }

    public static function isLocale($locale)
    {
        return self::getLocale() === $locale;
    }

    public static function get(string $key)
    {
        return TranslationManager::getInstance()->get($key);
    }
}
