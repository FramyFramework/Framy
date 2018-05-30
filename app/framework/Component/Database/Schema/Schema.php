<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Schema;
    use app\framework\Component\Database\Medoo;

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
         * @var Medoo
         */
        private static $Medoo;

        /**
         * Create a table based on Blueprint.
         *
         * @param string $name
         * @param callable $call
         */
        static public function create(string $name, callable $call)
        {
            self::init();

            $blueprint = new Blueprint($name);
            call_user_func($call, $blueprint);

            self::$Medoo->query(Builder::createTable($blueprint));
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

        private static function init()
        {
            if(is_null(self::$Medoo))
                self::$Medoo = new Medoo();

            // TODO: Remove later
            self::$Medoo->debug();
        }
    }