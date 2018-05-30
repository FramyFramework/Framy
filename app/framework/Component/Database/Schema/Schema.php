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
         * Instance of Medoo;
         * @var Medoo
         */
        private static $Medoo;

        /**
         * Create a table based on Blueprint.
         *
         * @param string $name
         * @param callable $call
         * @param string $connection NOTE: will be changed!
         */
        static public function create(string $name, callable $call, string $connection = null)
        {
            self::init($connection);

            $blueprint = new Blueprint($name);
            call_user_func($call, $blueprint);

            self::$Medoo->query(Builder::createTable($blueprint));
        }

        static public function drop(string $name, string $connection = null)
        {
            self::init($connection);

            Builder::dropTable($name);
        }

/* TODO: add methods
        static public function dropIfExists(string $name, string $connection = null)
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
*/
        private static function init(string $connection = null)
        {
            if(is_null(self::$Medoo))
                self::$Medoo = new Medoo($connection);
        }
    }