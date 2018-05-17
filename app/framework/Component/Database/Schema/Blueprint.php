<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Schema;

    /**
     * Blueprint of an Database table
     *
     * @package app\framework\Component\Database\Schema
     */
    class Blueprint
    {
        /**
         * The Table that the Blueprint describes
         *
         * @var string
         */
        private $table;

        /**
         * The columns that should be added to the table.
         *
         * @var Column[]
         */
        private $columns = [];

        /**
         * @var string
         */
        private $engine;

        /**
         * @var
         */
        private $charset;

        /**
         * @var
         */
        private $collation;

        public function __construct($name)
        {
            $this->table = $name;
        }

        /**
         * @return string
         */
        public function getTable(): string
        {
            return $this->table;
        }

        /**
         * @return Column[]
         */
        public function getColumns(): array
        {
            return $this->columns;
        }

        /**
         * BOOLEAN equivalent column.
         */
        public function boolean(){}

        /**
         * BIGINT equivalent column.
         */
        public function bigInteger(){}

        /**
         * Auto-incrementing UNSIGNED INTEGER (primary key) equivalent column.
         *
         * @param string $name
         */
        public function increments($name = 'id')
        {
            $tempColumn = new Column($name, 'INT');
            $tempColumn->isAutoIncrement = true;
            $tempColumn->isPrimaryKey    = true;
            $tempColumn->isUnsigned      = true;
            $this->columns[] = $tempColumn;
        }

        /**
         * Auto-incrementing UNSIGNED BIGINT (primary key) equivalent column.
         * @param
         */
        public function bigIncrements(){}

        /**
         * VARCHAR equivalent column with a optional length.
         *
         * @param string $name
         * @param int $length
         */
        public function string(string $name, int $length = 255)
        {
            $tempColumn = new Column($name, 'VARCHAR');
            $tempColumn->length = $length;
            $this->columns[] = $tempColumn;
        }

        /**
         * CHAR equivalent column with an optional length.
         *
         * @param string $name
         * @param int $length
         */
        public function char($name, $length)
        {
            $tempColumn = new Column($name, 'CHAR');
            $tempColumn->length = $length;
            $this->columns[] = $tempColumn;
        }

        /**
         * TEXT equivalent column.
         *
         * @param string $name
         */
        public function text($name)
        {
            $tempColumn = new Column($name, 'TEXT');
            $this->columns[] = $tempColumn;
        }

        /**
         * Adds nullable created_at and updated_at TIMESTAMP equivalent columns.
         */
        public function timestamps()
        {
            $this->timestamp("created_at", true);
            $this->timestamp("updated_at", true);
        }

        /**
         * TIMESTAMP equivalent column.
         *
         * @param string $name
         * @param bool   $isNullable
         */
        public function timestamp($name, $isNullable = false)
        {
            $tempColumn = new Column($name, 'TIMESTAMP');
            $tempColumn->isNull = $isNullable;
            $this->columns[] = $tempColumn;
        }

        /**
         * TIMESTAMP (with timezone) equivalent column.
         */
        public function timestampTz(){}

        /**
         * YEAR equivalent column.
         */
        public function year(){}

        /**
         * DATE equivalent column.
         */
        public function date(){}

        /**
         * DATETIME equivalent column.
         */
        public function dateTime(){}

        /**
         * DATETIME (with timezone) equivalent column.
         */
        public function dateTimeTz(){}

        /**
         * TIME equivalent column.
         */
        public function time(){}

        /**
         * BLOB equivalent column.
         */
        public function binary($data){}
    }