<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Connection;

    use app\framework\Component\Database\Model\Model;
    use app\framework\Component\EventManager\EventManagerTrait;
    use PDO;
    use PDOStatement;
    use app\framework\Component\Stopwatch\Stopwatch;
    use app\framework\Component\Stopwatch\StopwatchEvent;

    /**
     * Class Connection
     * Representing and holding an database connection.
     *
     * @package app\framework\Component\Database
     */
    class Connection
    {
        use EventManagerTrait;

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
            $this->pdo  = $pdo;
            $this->name = $name;

            // first we will setup the default properties.
            // We will keep track of the database name
            // as it's required in some commands
            $this->database = $database;

            $this->config = $conf;
        }

        /**
         * Get the connection query log.
         *
         * @return array
         */
        public function getQueryLog()
        {
            return $this->queryLog;
        }

        /**
         * Clear the query log.
         *
         * @return void
         */
        public function flushQueryLog()
        {
            $this->queryLog = [];
        }

        /**
         * Enable the query log on the connection.
         *
         * @return void
         */
        public function enableQueryLog()
        {
            $this->loggingQueries = true;
        }

        /**
         * Disable the query log on the connection.
         *
         * @return void
         */
        public function disableQueryLog()
        {
            $this->loggingQueries = false;
        }

        /**
         * Determine whether we're logging queries.
         *
         * @return bool
         */
        public function logging()
        {
            return $this->loggingQueries;
        }

        /**
         * Get the name of the connected database.
         *
         * @return string
         */
        public function getDatabaseName()
        {
            return $this->database;
        }

        /**
         * Set the name of the connected database.
         *
         * @param  string  $database
         */
        public function setDatabaseName($database)
        {
            $this->database = $database;
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
        {
            return $this->run($query);
        }

        public function update(string $query)
        {
            return $this->run($query);
        }

        public function insert(string $query)
        {
            return $this->run($query);
        }

        public function delete(string $query)
        {
            return $this->run($query);
        }

        /**
         * Run a SQL statement and log its execution context.
         *
         * @param string $query
         * @return mixed
         */
        protected function run(string $query)
        {
            $stopwatch = new Stopwatch();
            $result    = null;
            $stopwatch->start('queryRun');

            if (! $result = $this->pdo->query($query)) {
                handle( new \Exception("Syntax error in the query: '". $query."'"));
            } else {
                $result = $this->fetch($result);
            }

            $this->logQuery($query, $stopwatch->stop('queryRun'));
            return $result;
        }

        /**
         * Log a query in the connection's query log.
         *
         * @param string $query
         * @param StopwatchEvent $stopwatchEvent
         */
        public function logQuery(string $query, StopwatchEvent $stopwatchEvent)
        {
            $this->eventManager()->fire("ff.database.query_execution");

            if($this->loggingQueries) {
                $logEntry['startTime']     = $stopwatchEvent->getStartTime();
                $logEntry['query']         = $query;
                $logEntry['executionTime'] = $stopwatchEvent->getDuration();

                $this->queryLog[] = $logEntry;
            }
        }

        /**
         * Fetch PDO Result and return and array of Models or a single one
         * @param PDOStatement $statement
         * @return Model[]|Model
         */
        public function fetch(PDOStatement $statement)
        {
            $result    = [];
            $statement = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($statement as $key => $value) {
                $result[$key] = new Model($this);
                $result[$key]->fillData($value);
            }

            if(sizeof($result) == 1)
                $result = $result[0];

            return $result;
        }
    }