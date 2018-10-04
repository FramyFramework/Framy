<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Storage\Disk;

    use app\framework\Component\StdLib\StdLibTrait;
    use app\framework\Component\Storage\StorageException;
    use app\framework\Component\Storage\Driver\Local\LocalStorageDriver;
    use app\framework\Component\Config\Config;

    class Disk implements DiskInterface
    {
        use StdLibTrait;

        private $disk;
        private $Driver;

        /**
         * Disk constructor.
         * @param null $diskName
         */
        function __construct($diskName = null)
        {
            if(strpos($diskName, '/') !== false) {
                $this->disk = $this->validateDisk([
                    'root' => $diskName
                ]);
            } else {
                if($this->isDisk($diskName)) {
                    $this->disk = $this->validateDisk(
                        Config::getInstance()->get("disks", "filesystem")[$diskName]
                    );
                } else {
                    handle(new StorageException(StorageException::DISK_NOT_FOUND));
                }
            }

            if($this->isNull($this->disk['driver']))
                $this->disk['driver'] = Config::getInstance()->get("default", "filesystem");

            if($this->disk['driver'] == 'local') {
                $this->Driver = new LocalStorageDriver($this->disk);
            }
        }

        /**
         * Returns true of false if disk Exists
         *
         * @param $diskName
         * @return bool
         */
        public function isDisk($diskName)
        {
            if(!$this->isNull(Config::getInstance()->get("disks", "filesystem")[$diskName])) {
                return true;
            } else {
                return false;
            }
        }

        public function keyExists($key)
        {
            return $this->Driver->keyExists($key);
        }

        /**
         * @return LocalStorageDriver
         */
        public function getDriver()
        {
            return $this->Driver;
        }

        /**
         * Returns Path or false if path is not defined in config file.
         *
         * @return String|bool
         */
        public function getPath()
        {
            return $this->disk['root'];
        }

        private function validateDisk($conf)
        {
            if(!array_key_exists('driver', $conf))
                $conf['driver'] = null;

            if(!array_key_exists('root', $conf))
                $conf['root'] = null;

            return $conf;
        }
    }