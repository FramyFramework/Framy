<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Query;

use Closure;
use app\framework\Component\Database\Connection\Connection;
use app\framework\Component\Database\Model\Model;
use app\framework\Component\Database\Query\Grammars\Grammar;
use Exception;
use InvalidArgumentException;

/**
 * Class Builder
 * Build Query based on grammar.
 *
 * @package app\framework\Component\Database\Query
 */
class Builder
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * The database query grammar instance.
     *
     * @var Grammar
     */
    private $grammar;

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    public $columns;

    /**
     * The current query value bindings.
     *
     * @var array
     */
    protected $bindings = [
        'select' => [],
        'join'   => [],
        'where'  => [],
        'having' => [],
        'order'  => [],
        'union'  => [],
    ];

    /**
     * An aggregate function and column to be run.
     *
     * @var array
     */
    public $aggregate;

    /**
     * Indicates if the query returns distinct results.
     *
     * @var bool
     */
    public $distinct = false;

    /**
     * The table which the query is targeting.
     *
     * @var string
     */
    public $from;

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    public $wheres;

    /**
     * The orderings for the query.
     *
     * @var array
     */
    public $orders;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    public $limit;

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];

    /**
     * Builder constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->grammar = new Grammar();

    }

    /**
     * Add an array of where clauses to the query.
     *
     * @param  array  $column
     * @param  string  $boolean
     * @return $this
     */
    protected function addArrayOfWheres($column, string $boolean)
    {
        return $this->whereNested(function ($query) use ($column) {

            /** @var Builder $query */
            foreach ($column as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    call_user_func_array([$query, 'where'], $value);
                } else {
                    $query->where($key, '=', $value);
                }
            }
        }, $boolean);
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @return Builder|static
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * Add an "and where" clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @return Builder|static
     */
    public function andWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'and');
    }

    /**
     * Determine if the given operator and value combination is legal.
     *
     * @param  string  $operator
     * @param  mixed  $value
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value)
    {
        $isOperator = in_array($operator, $this->operators);

        return $isOperator && $operator != '=' && is_null($value);
    }

    /**
     * Add a binding to the query.
     *
     * @param  mixed   $value
     * @param  string  $type
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function addBinding($value, $type = 'where')
    {
        if (! array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }

        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        } else {
            $this->bindings[$type][] = $value;
        }

        return $this;
    }

    /**
     * Get the current query value bindings in a flattened array.
     *
     * @return array
     */
    public function getBindings()
    {
        return arr($this->bindings)->flatten();
    }

    /**
     * Set the columns to be selected.
     *
     * @param  array|mixed  $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    public function selectRaw(string $query, array $bindings = [])
    {
        return $this->connection->select($query, $bindings);
    }

    public function insert(array $values)
    {
        if (empty($values)) {
            return true;
        }

        if (! is_array(reset($values))) {
            $values = [$values];
        }

        // Here, we will sort the insert keys for every record so that each insert is
        // in the same order for the record. We need to make sure this is the case
        // so there are not any errors or problems when inserting these records.
        else {
            foreach ($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }

        // We'll treat every insert like a batch insert so we can easily insert each
        // of the records into the database consistently. This will make it much
        // easier on the grammars to just handle one type of record insertion.
        $bindings = [];

        foreach ($values as $record) {
            foreach ($record as $value) {
                $bindings[] = $value;
            }
        }

        $sql = $this->grammar->compileInsert($this, $values);

        // Once we have compiled the insert statement's SQL we can execute it on the
        // connection and return a result as a boolean success indicator as that
        // is the same type of result returned by the raw connection instance.
        $bindings = $this->cleanBindings($bindings);

        return $this->connection->insert($sql);
    }

    public function insertRaw(string $query, array $bindings = [])
    {
        return $this->connection->insert($query, $bindings);
    }

    public function updateRaw(string $query, array $bindings = [])
    {
        return $this->connection->update($query, $bindings);
    }

    public function deleteRaw(string $query, array $bindings = [])
    {
        return $this->connection->delete($query, $bindings);
    }

    /**
     * Set the table which the query is targeting.
     *
     * @param  string  $table
     * @return $this
     */
    public function from($table)
    {
        $this->from = $table;

        return $this;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  string|array  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @param  string  $boolean
     * @return $this
     */
    public function where($column, $operator = "=", $value = null, $boolean = 'and')
    {
        // If the column is an array, we will assume it is an array of key-value pairs
        // and can add them each as a where clause. We will maintain the boolean we
        // received when the method was called and pass it into the nested where.
        if (is_array($column)) {
            return $this->addArrayOfWheres($column, $boolean);
        }

        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going. Otherwise, we'll require the operator to be passed in.
        if (func_num_args() == 2) {
            list($value, $operator) = [$operator, '='];
        } elseif ($this->invalidOperatorAndValue($operator, $value)) {
            throw new InvalidArgumentException('Illegal operator and value combination.');
        }

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if (! in_array(strtolower($operator), $this->operators, true)) {
            list($value, $operator) = [$operator, '='];
        }

        // If the value is a Closure, it means the developer is performing an entire
        // sub-select within the query and we will need to compile the sub-select
        // within the where clause to get the appropriate query record results.
        if ($value instanceof Closure) {
            return $this->whereSub($column, $operator, $value, $boolean);
        }

        // If the value is "null", we will just assume the developer wants to add a
        // where null clause to the query. So, we will allow a short-cut here to
        // that method for convenience so the developer doesn't have to check.
        if (is_null($value)) {
            return $this->whereNull($column, $boolean, $operator != '=');
        }

        // Now that we are working with just a simple query we can put the elements
        // in our array and add the query binding to our array of bindings that
        // will be bound to each SQL statements when it is finally executed.
        $type = 'Basic';

        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');

        if (! $value instanceof Expression) {
            $this->addBinding($value, 'where');
        }

        return $this;
    }

    /**
     * Add a where in statement to the query.
     *
     * @param $column
     * @param array $values
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereIn($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'in';

        $this->wheres[] = compact('column', 'type', 'boolean', 'not');

        $this->addBinding($values, 'where');

        return $this;
    }

    /**
     * Add a where not in statement to the query.
     *
     * @param $column
     * @param array $values
     * @param string $boolean
     * @return Builder
     */
    public function whereNotIn($column, array $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * Add a or where in statement to the query.
     *
     * @param $column
     * @param array $values
     * @return Builder
     */
    public function orWhereIn($column, array $values)
    {
        return $this->whereIn($column, $values, 'or');
    }

    /**
     * Add a or where not in statement to the query.
     *
     * @param $column
     * @param array $values
     * @return Builder
     */
    public function orWhereNotIn($column, array $values)
    {
        return $this->whereIn($column, $values, 'or', true);
    }

    /**
     * Add a where between statement to the query.
     *
     * @param  string  $column
     * @param  array   $values
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        $this->wheres[] = compact('column', 'type', 'boolean', 'not');

        $this->addBinding($values, 'where');

        return $this;
    }

    /**
     * Add a or where between statement to the query.
     *
     * @param $column
     * @param array $values
     * @return Builder
     */
    public function orWhereBetween($column, array $values)
    {
        return $this->whereBetween($column, $values, 'or');
    }

    /**
     * Add a where not between statement to the query.
     *
     * @param $column
     * @param array $values
     * @param string $boolean
     * @return Builder
     */
    public function whereNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    /**
     * Add a or where not between statement to the query.
     *
     * @param $column
     * @param array $values
     * @return Builder
     */
    public function orWhereNotBetween($column, array $values)
    {
        return $this->whereBetween($column, $values, 'or', true);
    }

    /**
     * Add a nested where statement to the query.
     *
     * @param  Closure $callback
     * @param  string   $boolean
     * @return Builder|static
     */
    public function whereNested(Closure $callback, $boolean = 'and')
    {
        $query = $this->forNestedWhere();

        call_user_func($callback, $query);

        return $this->addNestedWhereQuery($query, $boolean);
    }

    /**
     * Create a new query instance for nested where condition.
     *
     * @return Builder
     */
    public function forNestedWhere()
    {
        $query = $this->newQuery();

        return $query->from($this->from);
    }

    /**
     * Add another query builder as a nested where to the query builder.
     *
     * @param  Builder|static $query
     * @param  string  $boolean
     * @return $this
     */
    public function addNestedWhereQuery($query, $boolean = 'and')
    {
        if (count($query->wheres)) {
            $type = 'Nested';

            $this->wheres[] = compact('type', 'query', 'boolean');

            $this->addBinding($query->getBindings(), 'where');
        }

        return $this;
    }

    /**
     * Add a full sub-select to the query.
     *
     * @param  string   $column
     * @param  string   $operator
     * @param  Closure $callback
     * @param  string   $boolean
     * @return $this
     */
    protected function whereSub($column, $operator, Closure $callback, $boolean)
    {
        $type = 'Sub';

        $query = $this->newQuery();

        // Once we have the query instance we can simply execute it so it can add all
        // of the sub-select's conditions to itself, and then we can cache it off
        // in the array of where clauses for the "main" parent query instance.
        call_user_func($callback, $query);

        $this->wheres[] = compact('type', 'column', 'operator', 'query', 'boolean');

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }

    /**
     * Add a "where null" clause to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @param  bool    $not
     * @return $this
     */
    public function whereNull($column, $boolean = 'and', $not = false)
    {
        $type = 'Null';

        $this->wheres[] = compact('type', 'column', 'boolean', 'not');

        return $this;
    }

    /**
     * Add an "or where null" clause to the query.
     *
     * @param  string  $column
     * @return Builder|static
     */
    public function orWhereNull($column)
    {
        return $this->whereNull($column, 'or');
    }

    /**
     * Add a "where not null" clause to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @return Builder|static
     */
    public function whereNotNull($column, $boolean = 'and')
    {
        return $this->whereNull($column, $boolean, true);
    }

    /**
     * Add an "or where not null" clause to the query.
     *
     * @param  string  $column
     * @return Builder|static
     */
    public function orWhereNotNull($column)
    {
        return $this->whereNull($column, 'or', true);
    }

    /**
     * Add a "where date" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return Builder|static
     */
    public function whereDate($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Date', $column, $operator, $value, $boolean);
    }

    /**
     * Add an "or where date" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @return Builder|static
     */
    public function orWhereDate($column, $operator, $value)
    {
        return $this->whereDate($column, $operator, $value, 'or');
    }

    /**
     * Add a "where day" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return Builder|static
     */
    public function whereDay($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Day', $column, $operator, $value, $boolean);
    }

    /**
     * Add a "where month" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return Builder|static
     */
    public function whereMonth($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Month', $column, $operator, $value, $boolean);
    }

    /**
     * Add a "where year" statement to the query.
     *
     * @param  string  $column
     * @param  string   $operator
     * @param  int   $value
     * @param  string   $boolean
     * @return Builder|static
     */
    public function whereYear($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Year', $column, $operator, $value, $boolean);
    }

    /**
     * Add a date based (year, month, day) statement to the query.
     *
     * @param  string  $type
     * @param  string  $column
     * @param  string  $operator
     * @param  int  $value
     * @param  string  $boolean
     * @return $this
     */
    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean = 'and')
    {
        $this->wheres[] = compact('column', 'type', 'boolean', 'operator', 'value');

        $this->addBinding($value, 'where');

        return $this;
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param  array|string  $column
     * @param  string        $direction asc or desc
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        if(!($direction == 'asc' or $direction == 'desc')) {
            handle(new Exception("Order by direction invalid: '".$direction."'"));
        }

        $this->orders = [
            $column,
            $direction
        ];

        return $this;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     * @return array|null
     */
    public function get(array $columns = ['*'])
    {
        $this->columns = $columns;
//        dd($this->toSql());
        return $this->connection->select(
            $this->toSql(),
            $this->getBindings()
        );
    }

    /**
     * Return only first result from database
     *
     * @param array $columns
     * @return Model
     */
    public function first(array $columns = ['*'])
    {
        $this->limit = 1;

        return $this->get($columns)[0];
    }

    /**
     * To only get a single value
     *
     * @param $val
     * @return mixed
     */
    public function value(string $val)
    {
        return $this->first([$val])->$val;
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @param  string  $columns
     * @return int
     */
    public function count($columns = '*')
    {
        if (! is_array($columns)) {
            $columns = [$columns];
        }

        return (int) $this->aggregate(__FUNCTION__, $columns);
    }

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param  string  $column
     * @return float|int
     */
    public function min(string $column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the maximum value of a given column.
     *
     * @param  string  $column
     * @return float|int
     */
    public function max($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  string  $column
     * @return float|int
     */
    public function sum($column)
    {
        $result = $this->aggregate(__FUNCTION__, [$column]);

        return $result ?: 0;
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return float|int
     */
    public function avg($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Alias for the "avg" method.
     *
     * @param  string  $column
     * @return float|int
     */
    public function average($column)
    {
        return $this->avg($column);
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return Builder
     */
    public function newQuery()
    {
        return new static($this->connection);
    }

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    public function exists()
    {
        $sql = $this->grammar->compileExists($this);

        $results = $this->connection->select($sql);

        if (isset($results[0])) {
            $results = (array) $results[0];

            return (bool) $results['exists'];
        }

        return false;
    }

    /**
     * Determine if any rows does not exist for the current query.
     *
     * @return bool
     */
    public function doesntExist()
    {
        return ! $this->exists();
    }

    /**
     * Execute an aggregate function on the database.
     *
     * @param  string  $function
     * @param  array   $columns
     * @return float|int
     */
    public function aggregate($function, $columns = ['*'])
    {
        $this->aggregate = compact('function', 'columns');

        $previousColumns = $this->columns;

        $results = $this->get($columns);

        // Once we have executed the query, we will reset the aggregate property so
        // that more select queries can be executed against the database without
        // the aggregate value getting in the way when the grammar builds it.
        $this->aggregate = null;

        $this->columns = $previousColumns;

        if (isset($results[0])) {
            $result = array_change_key_case((array) $results[0]);

            return $result['aggregate'];
        }
    }

    /**
     * Check if the operator is in the list of valid operators.
     * Returns true if it is.
     *
     * @internal might be removed if it's not needed.
     * @param $operatorToCheck
     * @return bool
     */
    private function isOperatorValid($operatorToCheck)
    {
        foreach ($this->operators as $operator) {
            if($operatorToCheck === $operator) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the SQL representation of the query.
     *
     * @return string
     */
    private function toSql()
    {
        return $this->grammar->compileSelect($this);
    }

    /**
     * Remove all of the expressions from a list of bindings.
     *
     * @param  array  $bindings
     * @return array
     */
    protected function cleanBindings(array $bindings)
    {
        return array_values(array_filter($bindings, function ($binding) {
            return ! $binding instanceof Expression;
        }));
    }

    /**
     * Switch values in query with value fitting in binding
     *
     * @param string $query    The query to prepare
     * @param array $bindings  Array of the bindings
     * @return string          The run ready query
     */
    protected function prepareRawQuery(string $query, array $bindings): string
    {
        // search for values in query
        preg_match_all("/:([^ ]*)/", $query,$values);

        // replace values with bindings
        foreach ($values[1] as $value) {
            // check if fitting binding exists
            if(array_key_exists($value, $bindings)) {
                $query = str_replace(":".$value, $bindings[$value], $query);
            } else {
                handle(new Exception("Could not find fitting value '$value' in bindings for query: '$query'"));
            }
        }

        return $query;
    }
}
