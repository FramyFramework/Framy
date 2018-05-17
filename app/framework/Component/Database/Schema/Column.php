<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Schema;

    /**
     * Just an wrapper for DB columns.
     *
     * @package app\framework\Component\Database\Schema
     */
    class Column
    {
        public $name;
        public $type;
        public $value;
        public $length;
        public $comment;
        public $collation;
        public $isAutoIncrement;
        public $isNull;
        public $isPrimaryKey;
        public $isUnsigned;

        public function __construct($name, $type)
        {
            $this->name = $name;
            $this->type = $type;
        }
    }