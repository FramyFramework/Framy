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
        $tempColumn = new Column($name, 'BOOLEAN');
        $this->columns[] = $tempColumn;
    }

    /**
     * DECIMAL equivalent column with a precision (total digits) and scale (decimal digits).
     * @param string $name
     * @param int $precision
     * @param int $scale
     */
    public function decimal(string $name, int $precision, int $scale)
    {
        $tempColumn = new Column($name, 'DECIMAL');
        $tempColumn->length = $precision;
        $tempColumn->scale = $scale;
        $this->columns[] = $tempColumn;
    }

    /**
     * DOUBLE equivalent column with a precision (total digits) and scale (decimal digits).
     * @param string $name
     * @param int $precision
     * @param int $scale
     */
    public function double(string $name, int $precision, int $scale)
    {
        $tempColumn = new Column($name, 'DOUBLE');
        $tempColumn->length = $precision;
        $tempColumn->scale = $scale;
        $this->columns[] = $tempColumn;
    }

    /**
     * FLOAT equivalent column with a precision (total digits) and scale (decimal digits).
     * @param string $name
     * @param int $precision
     * @param int $scale
     */
    public function float(string $name, int $precision, int $scale)
    {
        $tempColumn = new Column($name, 'FLOAT');
        $tempColumn->length = $precision;
        $tempColumn->scale = $scale;
        $this->columns[] = $tempColumn;
    }

    /**
     * INTEGER equivalent column.
     *
     * @param string $name
     */
    public function integer($name)
    {
        $tempColumn = new Column($name, 'INTEGER');
        $this->columns[] = $tempColumn;
    }

    /**
     * MEDIUMINT equivalent column.
     *
     * @param string $name
     */
    public function mediumInteger($name)
    {
        $tempColumn = new Column($name, 'MEDIUMINT');
        $this->columns[] = $tempColumn;
    }

    /**
     * BIGINT equivalent column.
     *
     * @param string $name
     */
    public function bigInteger($name)
    {
        $tempColumn = new Column($name, 'BIGINT');
        $this->columns[] = $tempColumn;
    }

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
     *
     * @param string $name
     */
    public function bigIncrements($name = 'id')
    {
        $tempColumn = new Column($name, 'BIGINT');
        $tempColumn->isAutoIncrement = true;
        $tempColumn->isPrimaryKey    = true;
        $tempColumn->isUnsigned      = true;
        $this->columns[] = $tempColumn;
    }

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
     *
     * @param string $name
     */
    public function year($name)
    {
        $tempColumn = new Column($name, 'YEAR');
        $this->columns[] = $tempColumn;
    }

    /**
     * DATE equivalent column.
     *
     * @param string $name
     */
    public function date($name)
    {
        $tempColumn = new Column($name, 'DATE');
        $this->columns[] = $tempColumn;
    }

    /**
     * DATETIME equivalent column.
     *
     * @param string $name
     */
    public function dateTime($name)
    {
        $tempColumn = new Column($name, 'DATETIME');
        $this->columns[] = $tempColumn;
    }

    /**
     * DATETIME (with timezone) equivalent column.
     */
    public function dateTimeTz(){}

    /**
     * TIME equivalent column.
     *
     * @param string $name
     */
    public function time($name)
    {
        $tempColumn = new Column($name, 'TIME');
        $this->columns[] = $tempColumn;
    }

    /**
     * BLOB equivalent column.
     *
     * @param string $name
     */
    public function binary($name)
    {
        $tempColumn = new Column($name, 'BLOB ');
        $this->columns[] = $tempColumn;
    }
}
