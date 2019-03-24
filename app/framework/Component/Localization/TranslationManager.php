<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Localization;

use app\framework\Component\Stopwatch\Stopwatch;
use \RuntimeException;
use app\framework\Component\Config\Config;
use app\framework\Component\StdLib\SingletonTrait;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;
use app\framework\Component\Storage\Storage;

/**
 * Class TranslationManager
 * @package app\framework\Component\Localization
 */
class TranslationManager
{
    use SingletonTrait;

    private const FILE_EXTENSION = "php";

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $fallBackLocale;

    /**
     * @var array
     */
    private $cachedStrings = [];

    /**
     * Because Storage component is shit
     * @var string
     */
    private $langDir;

    /**
     * The local that has been used
     * @var string
     */
    private $usedLocale;

    /**
     * The contents of the lang file actually used
     * @var array
     */
    private $currentFile;

    /**
     * Replacing the __constructor() method
     */
    protected function init()
    {
        $this->locale         = Config::getInstance()->get("locale",         "app");
        $this->fallBackLocale = Config::getInstance()->get("fallBackLocale", "app");
        $this->langDir        = (new Storage("lang"))->getAbsolutePath();
    }

    /**
     * Return Cached Locale strings from locale or fallback locale, if locale isn't set.
     * @return array|null
     */
    public function getCached()
    {
        return $this->cachedStrings[$this->locale] ?: $this->cachedStrings[$this->fallBackLocale];
    }

    /**
     * Get translation string either from cache or from file.
     * Returns false if key is found in neither of the locale and fallback locale
     *
     * @param string $key Example [file_name].[first_key].[optional_second_key] <br>
     * messages.welcome
     * @return string|false
     */
    public function get(string $key)
    {
        $data = new ArrayObject(explode(".", $key));

        $fromCache = $this->getFromCache(clone $data);

        if ($fromCache) {
            $result = $fromCache;
            $data->removeFirst();
        } else {
            $result = $this->getFromFile($data);
        }

        if (is_array($result)) {
            $data->removeFirst();
            $result = $result[$data->first()];
        }

        // put in cache
        $this->addToCache();
        return $result;
    }

    /**
     * Try to get the value from cached values
     * @param $key
     * @return string|false
     */
    public function getFromCache(ArrayObject $key)
    {
        $cachedStrings = $this->getCached();

        $key->removeFirst();

        return $cachedStrings[$key->first()];
    }

    /**
     * Try to get the value from file values
     * @param $key
     * @return string|false
     */
    public function getFromFile(ArrayObject $key)
    {
        $localSpecificDirectory         = $this->langDir.DIRECTORY_SEPARATOR.$this->locale;
        $localSpecificDirectoryFallBack = $this->langDir.DIRECTORY_SEPARATOR.$this->fallBackLocale;

        if (! file_exists($localSpecificDirectory)) {
            throw new RuntimeException("Local does not exist");
        }

        // create path to file
        $file = DIRECTORY_SEPARATOR.$key->first().".".self::FILE_EXTENSION;

        $keySpecificPath         = $localSpecificDirectory.$file;
        $keySpecificPathFallBack = $localSpecificDirectoryFallBack.$file;

        $key->removeFirst();

        $result = $this->getFileData($keySpecificPath)[$key->first()] ?: false;

        if (!$result) {
            $this->usedLocale = $this->fallBackLocale;
            return $this->getFileData($keySpecificPathFallBack)[$key->first()] ?: false;
        }

        $this->usedLocale = $this->locale;
        return $result;

    }

    protected function getFileData($file)
    {
        if (file_exists($file)) {
            return $this->currentFile = require_once $file;
        } else {
            throw new RuntimeException("Lang file not found: <br>".$file);
        }
    }

    /**
     * Getter for locale
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Setter for locale
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    private function addToCache()
    {
        foreach ($this->currentFile as $key => $value) {
            $this->cachedStrings[$this->usedLocale][$key] = $value;
        }
    }
}
