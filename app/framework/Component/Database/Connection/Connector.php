<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Connection;

    /**
     * Class Connector
     * Manages the building of an new connection.
     *
     * @package app\framework\Component\Database\Connection
     */
    class Connector
    {
        public function createConnection($dsn, array $config)
        {
            list($username, $password) = [
                $config['username'] ?? null, $config['password'] ?? null,
            ];
        }

        /**
         * Create a new PDO connection instance.
         *
         * @param  string  $dsn
         * @param  string  $username
         * @param  string  $password
         * @param  array  $options
         *
         * @return \PDO
         */
        protected function createPdoConnection($dsn, $username, $password, $options)
        {

        }
    }