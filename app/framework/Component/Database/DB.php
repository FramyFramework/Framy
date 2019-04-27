<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database;

/**
 * Class DB
 * Simple class for accessing functionality of the Database component
 *
 * @package app\framework\Component\Database
 */
class DB
{
    public static function table(string $name)
    {
        return (new Manager)->table($name);
    }

    /**
     * To run a select query against the database
     *
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public static function select(string $query, array $bindings = [])
    {
        return (new Manager)->selectRaw($query, $bindings);
    }

    public static function insert(string $query, array $bindings = [])
    {
        return (new Manager)->insertRaw($query, $bindings);
    }

    public static function update(string $query, array $bindings = [])
    {
        return (new Manager)->updateRaw($query, $bindings);
    }

    public static function delete(string $query, array $bindings = [])
    {
        return (new Manager)->deleteRaw($query, $bindings);
    }
}
