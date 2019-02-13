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
    /**
     * To run a select query against the database
     *
     * @param string $query
     * @param array $bindings
     * @return array
     */
    static function select(string $query, array $bindings = [])
    {
        return (new Manager)->selectRaw($query, $bindings);
    }

    static function insert(string $query, array $bindings = [])
    {
        return (new Manager)->insertRaw($query, $bindings);
    }

    static function update(string $query, array $bindings = [])
    {
        return (new Manager)->updateRaw($query, $bindings);
    }


    static function delete(string $query, array $bindings = [])
    {
        return (new Manager)->deleteRaw($query, $bindings);
    }
}
