<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database;

    use app\framework\Component\Database\Connection\ConnectionFactory;

    /**
     * Class Manager
     * @package app\framework\Component\Database
     */
    class Manager
    {
        /**
         * @var array Connection
         */
        private $connections = [];

        /**
         * @var ConnectionFactory
         */
        private $connectionFactory;

        /**
         * Manager constructor.
         */
        public function __construct()
        {
            $this->connectionFactory = new ConnectionFactory();
        }

        /**
         * Set up specific connection.
         * @param string $name
         */
        public function addConnection(string $name)
        {
            $this->connections[$name] = $this->connectionFactory->make($name);
        }

        /**
         * Set up default connection.
         */
        public function useDefaultConn()
        {
            $connection = $this->connectionFactory->make();
            $this->connections[$connection->getName()] = $connection;
        }

        public function table(string $name)
        {
            return $this->connections['mysql'];
        }
    }