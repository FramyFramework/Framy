<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Connection;

    use app\framework\Component\Stopwatch\Stopwatch;
    use app\framework\Component\Stopwatch\StopwatchEvent;
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
         * The name of the connection.
         * @var string
         */
        protected $name;

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

        public function __construct(Pdo $pdo, string $database = '', string $name = '', array $conf = [])
        {
            $this->pdo = $pdo;

            $this->name = $name;

            // first we will setup the default properties.
            // We will keep track of the database name
            // as it's required in some commands
            $this->database = $database;

            $this->config = $conf;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getDriver()
        {
            return $this->config['driver'];
        }

        public function select(string $query)
        {}

        /**
         * Run a SQL statement and log its execution context.
         *
         * @param string $query
         */
        protected function run(string $query)
        {
            $stopwatch = new Stopwatch();
            $stopwatch->start('queryRun');

            //TODO: do more execution stuff
            $this->pdo->query($query);

            $this->logQuery($query, $stopwatch->stop('queryRun'));
        }

        /**
         * Log a query in the connection's query log.
         *
         * @param string $query
         * @param StopwatchEvent $stopwatchEvent
         */
        public function logQuery(string $query, StopwatchEvent $stopwatchEvent)
        {
            //todo: fire event of query execution

            if($this->loggingQueries) {
                $logEntry['startTime']     = $stopwatchEvent->getStartTime();
                $logEntry['query']         = $query;
                $logEntry['executionTime'] = $stopwatchEvent->getDuration();

                $this->queryLog[] = $logEntry;
            }
        }
    }