<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Query;

    use app\framework\Component\Database\Connection\Connection;
    use app\framework\Component\Database\Model\Model;
    use app\framework\Component\Database\Query\Grammars\Grammar;

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

        private $grammar;

        private $table;

        /**
         * The columns that should be returned.
         *
         * @var array
         */
        public $columns;

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

        /* TODO: do we need this method?! */
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
         * @param  string|array  $column
         * @param  string  $operator
         * @param  mixed   $value
         * @param  string  $bool
         * @return $this
         */
        public function where($column, $operator = "=", $value = null, $bool = 'and')
        {
            // check if operator is valid
            if(!$this->isOperatorValid($operator)) {
                handle(new \Exception("Where operator not valid: '".$operator."'"));
            }

            // Add prepared data to where array
            $this->wheres[] = [
                $column,
                $operator,
                $value,
                $bool
            ];

            return $this;
        }

        /**
         * Add an "order by" clause to the query.
         * TODO: implement:
         *
         * @param  string  $column
         * @param  string  $direction
         * @return $this
         */
        public function orderBy($column, $direction = 'asc')
        {
            return $this;
        }

        /**
         * Execute the query as a "select" statement.
         *
         * @return Model|null
         */
        public function get()
        {
            return $this->connection->select(
                $this->toSql()
            );
        }

        /**
         * Check if the operator is in the list of valid operators.
         * Returns true if it is.
         *
         * @param $operatorToCheck
         * @return bool
         */
        private function isOperatorValid($operatorToCheck)
        {
            foreach ($this->operators as $operator) {
                if($operatorToCheck === $operator)
                    return true;
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
    }