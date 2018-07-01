<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Query;
    use app\framework\Component\Database\Connection\Connection;

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

        private $driver;

        private $table;

        /**
         * The columns that should be returned.
         *
         * @var array
         */
        public $columns;

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
            $this->driver = $connection->getDriver();
        }

        public function table(string $name)
        {
            $this->table = $name;
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
         * @param  string|array|\Closure  $column
         * @param  string  $operator
         * @param  mixed   $value
         * @return $this
         */
        public function where($column, $operator = null, $value = null)
        {
            return $this;
        }

        /**
         * Add an "order by" clause to the query.
         *
         * @param  string  $column
         * @param  string  $direction
         * @return $this
         */
        public function orderBy($column, $direction = 'asc')
        {
            return $this;
        }
    }