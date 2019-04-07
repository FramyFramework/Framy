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
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;
use app\framework\Component\Stopwatch\Stopwatch;
use app\framework\Component\Stopwatch\StopwatchEvent;
use Closure;
use Exception;
use PDO;
use PDOStatement;

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
    protected $loggingQueries = true;

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

    public function select(string $query, array $bindings = [], $table = "")
    {
        return $this->run($query, $bindings, function ($me, $query, $bindings) use ($table) {
            // For select statements, we'll simply execute the query and return an array
            // of the database result set. Each element in the array will be a single
            // row from the database table, and will either be an array or objects.
            /** @var Connection $me */
            $statement = $me->pdo->prepare($query);

            $statement->execute($bindings);

            return arr($statement->fetchAll(PDO::FETCH_CLASS, $this->getNeededModel($table)));
        });
    }

    /**
     * Run an insert statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return bool
     */
    public function insert($query, $bindings = [])
    {
        return $this->statement($query, $bindings);
    }

    /**
     * Run an update statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     */
    public function update($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     */
    public function delete($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return bool
     */
    public function statement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($me, $query, $bindings) {
            return $me->pdo->prepare($query)->execute($bindings);
        });
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     */
    public function affectingStatement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($me, $query, $bindings) {

            // For update or delete statements, we want to get the number of rows affected
            // by the statement and return that back to the developer. We'll first need
            // to execute the statement and then we'll use PDO to fetch the affected.
            /** @var Connection $me */
            $statement = $me->pdo->prepare($query);

            $statement->execute($bindings);

            return $statement->rowCount();
        });
    }

    /**
     * Run a SQL statement and log its execution context.
     *
     * @param string $query
     * @param array $bindings
     * @param Closure $callback
     * @throws
     * @return mixed
     */
    protected function run(string $query, array $bindings, Closure $callback)
    {
        $stopwatch = new Stopwatch();
        $result    = null;
        $stopwatch->start('queryRun');

        try {
            $result = $this->runQueryCallback($query, $bindings, $callback);
        } catch (Exception $e) {
            handle($e);
        }

        $this->logQuery($query, $stopwatch->stop('queryRun'));
        return $result;
    }

    /**
     * Run a SQL statement.
     *
     * @param  string    $query
     * @param  array     $bindings
     * @param  Closure  $callback
     * @return mixed
     *
     * @throws Exception
     */
    protected function runQueryCallback($query, $bindings, Closure $callback)
    {
        // To execute the statement, we'll simply call the callback, which will actually
        // run the SQL against the PDO connection. Then we can calculate the time it
        // took to execute and log the query SQL, bindings and time in our memory.
        try {
            $result = $callback($this, $query, $bindings);
        }

        // If an exception occurs when attempting to run a query, we'll format the error
        // message to include the bindings with SQL, which will make this exception a
        // lot more helpful to the developer instead of just the database's errors.
        catch (Exception $e) {
            throw new Exception(
                $query, $bindings, $e
            );
        }

        return $result;
    }

    /**
     * Log a query in the connection's query log.
     *
     * @param string $query
     * @param StopwatchEvent $stopwatchEvent
     * @throws StringObjectException
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
     * Fetch PDO Result and return and array of Models
     *
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

        return $result;
    }

    /**
     * Returns the fully qualified class name of the model,
     * if it doesn't exist or is not where it should be the
     * base model is returned.
     *
     * @param $table
     * @return string
     */
    private function getNeededModel($table)
    {
        $model = "app\custom\Models\\".str($table)->charFirstUpper();

        return class_exists($model)
            ? $model
            : Model::class;
    }
}
