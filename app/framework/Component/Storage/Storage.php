<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Storage;

use app\framework\Component\StdLib\StdObject\DateTimeObject\DateTimeObject;
use app\framework\Component\Storage\Disk\Disk;
use app\framework\Component\Storage\Driver\AbsolutePathInterface;
use app\framework\Component\Storage\Driver\DirectoryAwareInterface;
use app\framework\Component\Storage\Driver\SizeAwareInterface;
use app\framework\Component\Storage\Driver\TouchableInterface;

/**
 * Storage class handles file storage through different storage drivers.
 *
 * @package app\framework\Component\Storage
 */
class Storage
{
    private $disk = null;

    function __construct($diskName)
    {
        $this->disk = new Disk($diskName);
    }

    public function keyExists($key)
    {
        return $this->disk->keyExists($key);
    }

    public function getDriver()
    {
        return $this->disk->getDriver();
    }

    public function getDisk()
    {
        return $this->disk;
    }

    public function getURL($key)
    {
        if (!$this->isDirectory($key)) {
            return $this->getDriver()->getURL($key);
        }

        return false;
    }

    /**
     * Reads the contents of the file
     *
     * @param string $key
     *
     * @return string|bool if cannot read content
     * @throws StorageException
     */
    public function getContents($key)
    {
        return $this->getDriver()->getContents($key);
    }

    /**
     * Writes the given contents into the file
     *
     * @param string $key
     * @param string $contents
     *
     * @param bool $append
     *
     * @return bool|int The number of bytes that were written into the file
     * @throws StorageException
     */
    public function setContents($key, $contents, $append = false)
    {
        return $this->getDriver()->setContents($key, $contents, $append);
    }

    /**
     * Returns an array of all keys (files and directories)
     *
     * @param string $key Key of a directory to get keys from. If not set - keys will be read from the storage root.
     *
     * @param bool|int   $recursive false = non-recursive, true = recursive, int = recursion depth
     *
     * @return array
     */
    public function getKeys($key = '', $recursive = false)
    {
        return $this->getDriver()->getKeys($key, $recursive);
    }

    /**
     * Create file with empty content
     *
     * @param $key
     * @return true on success
     * @throws StorageException
     */
    public function createKey($key)
    {
        return $this->getDriver()->crateKey($key);
    }

    /**
     * Returns the last modified time
     *
     * @param string $key
     *
     * @param bool $asDateTimeObject (Optional) Return as DateTimeObject if true
     *
     * @return bool|int|DateTimeObject
     * @throws StorageException
     */
    public function getTimeModified($key, $asDateTimeObject = false)
    {
        $time = $this->getDriver()->getTimeModified($key);

        if ($asDateTimeObject) {
            $datetime = new DateTimeObject();

            return $datetime->setTimestamp($time);
        }

        return $time;
    }

    /**
     * Deletes the file
     *
     * @param string $key
     *
     * @return bool
     * @throws StorageException
     */
    public function deleteKey($key)
    {
        return $this->getDriver()->deleteKey($key);
    }

    /**
     * Renames a file
     *
     * @param string $sourceKey Old key
     * @param string $targetKey New key
     *
     * @return bool
     * @throws StorageException
     */
    public function renameKey($sourceKey, $targetKey)
    {
        return $this->getDriver()->renameKey($sourceKey, $targetKey);
    }

    /**
     * Check if key is a directory
     *
     * @param string $key
     *
     * @return bool
     * @throws StorageException
     */
    public function isDirectory(string $key):bool
    {
        if ($this->supportsDirectories()) {
            return ($this->getDriver()->isDirectory($key));
        }

        return false;
    }

    /**
     * Touch a file (change time modified)<br />
     * Requires '\app\framework\Component\Storage\Driver\TouchableInterface' to be implemented by a Driver class
     *
     * @param string $key
     * @return mixed|void
     * @throws StorageException
     */
    public function touchKey($key)
    {
        if ($this->supportsTouching()) {
            return $this->getDriver()->touchKey($key);
        }
    }

    /**
     * Get file size<br />
     * Requires '\app\framework\Component\Storage\Driver\SizeAwareInterface' to be implemented by a Driver class
     *
     * @param string $key
     * @return int|bool The size of the file in bytes or false
     * @throws StorageException
     */
    public function getSize($key)
    {
        if ($this->supportsSize()) {
            return $this->getDriver()->getSize($key);
        }

        throw new StorageException(
            StorageException::DRIVER_CAN_NOT_ACCESS_SIZE,
            [get_class($this->getDriver())]
        );
    }

    /**
     * Get absolute file path<br />
     * Requires '\app\framework\Component\Storage\Driver\AbsolutePathInterface' to be implemented by a Driver class
     *
     * @param $key
     * @return mixed
     * @throws StorageException
     */
    public function getAbsolutePath($key = '')
    {
        if ($this->supportsAbsolutePaths()) {
            return $this->getDriver()->getAbsolutePath($key);
        }

        throw new StorageException(StorageException::DRIVER_DOES_NOT_SUPPORT_ABSOLUTE_PATHS, [
                get_class($this->getDriver())
            ]
        );
    }

    public function getRecentKey()
    {
        return $this->disk->getDriver()->getRecentKey();
    }

    /**
     * Can this storage handle directories?
     * @return bool
     */
    public function supportsDirectories()
    {
        return $this->disk->getDriver() instanceof DirectoryAwareInterface;
    }

    /**
     * Can this storage touch a file?
     * @return bool
     */
    public function supportsTouching()
    {
        return $this->disk->getDriver() instanceof TouchableInterface;
    }

    /**
     * Can this storage handle absolute paths?
     * @return bool
     */
    public function supportsAbsolutePaths()
    {
        return $this->disk->getDriver() instanceof AbsolutePathInterface;
    }

    /**
     * Can this storage get file size info?
     * @return bool
     */
    public function supportsSize()
    {
        return $this->disk->getDriver() instanceof SizeAwareInterface;
    }
}
