<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Schema;

    /**
     * Schema builder.
     * Used to build all the query's which Medoo can't build.
     * Like drop table or create table.
     *
     * @package app\framework\Component\Database\Schema
     */
    class Schema
    {
        /**
         * Create a table based on Blueprint.
         *
         * @param string $name
         * @param callable $call
         */
        static public function create(string $name, callable $call)
        {
            $blueprint = new Blueprint($name);
            call_user_func($call, $blueprint);

            dd(Builder::createTable($blueprint));
        }

        static public function drop(string $name)
        {
            Builder::dropTable($name);
        }

        static public function dropIfExists(string $name)
        {
            //TODO
        }

        static public function hasTable(string $name)
        {
            //TODO
        }
        static public function hasColumn(string $name)
        {
            //TODO
        }
    }