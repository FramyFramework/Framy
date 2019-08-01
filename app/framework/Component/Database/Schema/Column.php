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
    /**
     * @var string
     */
    public $name;

    /**
     * Type of column
     *
     * @var string
     */
    public $type;

    /**
     * Default length for e.g. integer
     *
     * @var int
     */
    public $length;

    /**
     * For e.g. DECIMAL
     *
     * @var int
     */
    public $scale;

    /**
     * To comment on the column
     *
     * @var string
     */
    public $comment;

    /**
     * Rather the value is signed or not -> numerical
     *
     * @var bool
     */
    public $isUnsigned;

    /**
     * Charset to set only for text
     *
     * @var string
     */
    public $charset;

    /**
     * @var string
     */
    public $collation;

    /**
     * @var bool
     */
    public $isAutoIncrement;

    /**
     * Ensures that a column cannot have a NULL value
     *
     * @var bool
     */
    public $notNull;

    /**
     * Sets a default value for a column when no value is specified
     *
     * @var Mixed
     */
    public $default;

    /**
     * @var bool
     */
    public $unique;

    /**
     * A combination of a NOT NULL and UNIQUE. Uniquely identifies each row in a table
     *
     * @var bool
     */
    public $primaryKey;

    /**
     * Uniquely identifies a row/record in another table
     *
     * @var bool
     */
    public $foreignKey;

    /**
     * Ensures that all values in a column satisfies a specific condition
     *
     * @var string
     */
    public $check;

    /**
     * Used to create and retrieve data from the database very quickly
     *
     * @var bool
     */
    public $index;

    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Specify a "default" value for the column
     *
     * @param $value
     * @return Column
     */
    public function default($value)
    {
        $this->default = $value;

        return $this;
    }

    public function unsigned()
    {
        $this->isUnsigned = true;
    }

    /**
     * Allows (by default) NULL values to be inserted into the column
     *
     * @return $this
     */
    public function nullable()
    {
        $this->notNull = false;
        return $this;
    }

    public function notNull($notNull = true)
    {
        $this->notNull = $notNull;
        return $this;
    }

    public function charset(string $charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Set INTEGER columns as auto-increment (primary key)
     *
     * @return $this
     */
    public function autoIncrement()
    {
        $this->isAutoIncrement = true;
        return $this;
    }

    public function primaryKey()
    {
        $this->primaryKey = true;
        return $this;
    }

    /**
     * Add a comment to a column
     *
     * @param string $text
     * @return $this
     */
    public function comment(string $text)
    {
        $this->comment = $text;
        return $this;
    }

    /**
     * Set TIMESTAMP columns to use CURRENT_TIMESTAMP as default value
     */
    public function useCurrent()
    {
    }
}
