<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database;

    use app\framework\Component\Database\Connection\Connection;
    use app\framework\Component\Database\Connection\ConnectionFactory;
    use app\framework\Component\Database\Query\Builder;

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
         * @var string $connectionToUse
         */
        private $connectionToUse;

        /**
         * @var ConnectionFactory
         */
        private $ConnectionFactory;

        /**
         * @var Builder
         */
        private $QueryBuilder;

        /**
         * Manager constructor.
         */
        public function __construct()
        {
            $this->ConnectionFactory = new ConnectionFactory();
        }

        /**
         * Set up connection.
         * @param string $name
         */
        public function addConnection(string $name = null)
        {
            $connection = $this->ConnectionFactory->make($name);
            $this->connections[$connection->getName()] = $connection;
        }

        /**
         * Return the connection or false if connection doesn't exist.
         *
         * @param string|null $name Name of the connection can be null if only one connection is configured.
         * @return false|Connection
         */
        public function getConnection(string $name = null)
        {
            if($name == null) {
                reset($this->connections);
                $Connection = $this->connections[key($this->connections)];
            } else {
                $Connection = $this->connections[$name];
            }

            if(isset($Connection)) {
                return $Connection;
            } else {
                return false;
            }
        }

        /**
         * Specify what connection shall be used.
         * @param string $name Leave empty to use default
         * @return Manager
         */
        public function useConnection(string $name = null): Manager
        {
            if($name) {
                $this->connectionToUse = $this->getConnection($name)->getName();
            } else {
                $this->connectionToUse = $this->getConnection()->getName();
            }

            $this->QueryBuilder = new Builder($this->connections[$this->connectionToUse]);

            return $this;
        }

        /**
         * @param array $columns
         * @return Builder
         */
        public function select($columns = ['*'])
        {
            // use default connection
            if($this->connectionToUse == null)
                $this->useConnection();

            $this->QueryBuilder->select($columns);

            return $this->QueryBuilder;
        }
    }