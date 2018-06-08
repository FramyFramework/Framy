<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Connection;

    use PDO;

    /**
     * Class Connection
     * Representing and holding an database connection.
     *
     * @package app\framework\Component\Database
     */
    class Connection
    {
        /**
         * The active pdo connection
         * @var PDO
         */
        protected $pdo;

        /**
         * The name of the connected database.
         *
         * @var string
         */
        protected $database;

        /**
         * The database connection configuration options.
         *
         * @var array
         */
        protected $config = [];

        /**
         * All of the queries run against the connection.
         *
         * @var array
         */
        protected $queryLog = [];

        /**
         * Indicates whether queries are being logged.
         *
         * @var bool
         */
        protected $loggingQueries = false;

        public function __construct(Pdo $pdo, string $database = '', array $conf = [])
        {
            $this->pdo = $pdo;

            // first we will setup the default properties.
            // We will keep track of the database name
            // as it's required in some commands
            $this->database = $database;

            $this->config = $conf;
        }
    }