<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Connection;

    class ConnectionResolver
    {
        /**
         * All of the registered connections.
         *
         * @var array
         */
        protected $connections = [];

        /**
         * Create a new connection resolver instance.
         *
         * @param  array  $connections
         * @return void
         */
        public function __construct(array $connections = [])
        {
            foreach ($connections as $name => $connection) {
                $this->addConnection($name, $connection);
            }
        }

        /**
         * Get a database connection instance.
         *
         * @param  string  $name
         * @return Connection
         */
        public function connection($name = null)
        {
            return $this->connections[$name];
        }

        /**
         * Add a connection to the resolver.
         *
         * @param $name
         * @param Connection $connection
         */
        public function addConnection($name, Connection $connection)
        {
            $this->connections[$name] = $connection;
        }

        /**
         * Check if a connection has been registered.
         *
         * @param  string  $name
         * @return bool
         */
        public function hasConnection($name)
        {
            return isset($this->connections[$name]);
        }
    }