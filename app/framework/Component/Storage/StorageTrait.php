<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

 namespace app\framework\Component\Storage;

 use app\framework\Component\Storage\Directory\Directory;
 use app\framework\Component\Storage\File\File;

 /**
  * A library of Storage functions
  *
  * @package app\framework\Component\Storage
  */
 trait StorageTrait
 {
     /**
      * Get storage
      *
      * @param $storageName
      * @return Storage
      */
     protected static function storage($storageName)
     {
         return new Storage($storageName);
     }

     protected function file($key, $storageName)
     {
         return new File($key, self::storage($storageName));
     }

     protected function directory($key, $storageName, $recursive = false, $filter = null)
     {
         return new Directory($key, self::storage($storageName), $recursive, $filter);
     }
 }
