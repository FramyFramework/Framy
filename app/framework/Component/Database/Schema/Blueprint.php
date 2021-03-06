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
     *
     * @param string $name
     */
    public function boolean($name)
    {
        $this->columns[] = $column = new Column($name, 'BOOLEAN');
    }

    /**
     * DECIMAL equivalent column with a precision (total digits) and scale (decimal digits).
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return Column
     */
    public function decimal(string $name, int $precision, int $scale = 0)
    {
        $tempColumn = new Column($name, 'DECIMAL');
        $tempColumn->length = $precision;
        $tempColumn->scale = $scale;
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * DOUBLE equivalent column with a precision (total digits) and scale (decimal digits).
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return Column
     */
    public function double(string $name, int $precision, int $scale)
    {
        $tempColumn = new Column($name, 'DOUBLE');
        $tempColumn->length = $precision;
        $tempColumn->scale = $scale;
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * FLOAT equivalent column with a precision (total digits) and scale (decimal digits).
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return Column
     */
    public function float(string $name, int $precision, int $scale)
    {
        $tempColumn = new Column($name, 'FLOAT');
        $tempColumn->length = $precision;
        $tempColumn->scale = $scale;
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * INTEGER equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function integer($name)
    {
        $tempColumn = new Column($name, 'INTEGER');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * MEDIUMINT equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function mediumInteger($name)
    {
        $tempColumn = new Column($name, 'MEDIUMINT');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * BIGINT equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function bigInteger($name)
    {
        $tempColumn = new Column($name, 'BIGINT');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * Auto-incrementing UNSIGNED INTEGER (primary key) equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function increments($name = 'id')
    {
        $tempColumn = new Column($name, 'INT');
        $tempColumn->isAutoIncrement = true;
        $tempColumn->primaryKey    = true;
        $tempColumn->isUnsigned      = true;
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * Auto-incrementing UNSIGNED BIGINT (primary key) equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function bigIncrements($name = 'id')
    {
        $tempColumn = new Column($name, 'BIGINT');
        $tempColumn->autoIncrement();
        $tempColumn->primaryKey();
        $tempColumn->isUnsigned = true;
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * VARCHAR equivalent column with a optional length.
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function string(string $name, int $length = 255)
    {
        $tempColumn = new Column($name, 'VARCHAR');
        $tempColumn->length = $length;
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * CHAR equivalent column with an optional length.
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function char($name, $length)
    {
        $tempColumn = new Column($name, 'CHAR');
        $tempColumn->length = $length;
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * TEXT equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function text($name)
    {
        $tempColumn = new Column($name, 'TEXT');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * Adds nullable created_at and updated_at TIMESTAMP equivalent columns.
     */
    public function timestamps()
    {
        $this->timestamp("created_at", true)->useCurrent();
        $this->timestamp("updated_at", true);
    }

    /**
     * TIMESTAMP equivalent column.
     *
     * @param string $name
     * @param bool $notNull
     * @return Column
     */
    public function timestamp($name, $notNull = false)
    {
        $tempColumn = new Column($name, 'TIMESTAMP');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * TIMESTAMP (with timezone) equivalent column.
     */
    //public function timestampTz(){}

    /**
     * YEAR equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function year($name)
    {
        $tempColumn = new Column($name, 'YEAR');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * DATE equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function date($name)
    {
        $tempColumn = new Column($name, 'DATE');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * DATETIME equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function dateTime($name)
    {
        $tempColumn = new Column($name, 'DATETIME');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * DATETIME (with timezone) equivalent column.
     */
    //public function dateTimeTz(){}

    /**
     * TIME equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function time($name)
    {
        $tempColumn = new Column($name, 'TIME');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }

    /**
     * BLOB equivalent column.
     *
     * @param string $name
     * @return Column
     */
    public function binary($name)
    {
        $tempColumn = new Column($name, 'BLOB ');
        $this->columns[] = $tempColumn;

        return $tempColumn;
    }
}
