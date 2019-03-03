<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Query\Grammars;

use app\framework\Component\Database\Query\Builder;
use app\framework\Component\Database\Query\Expression;

class Grammar
{
    /**
     * The grammar table prefix.
     *
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * The components that make up a select clause.
     *
     * @var array
     */
    protected $selectComponents = [
        'aggregate',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'unions',
        'lock',
    ];

    /**
     * Compile a select query into SQL.
     *
     * @param  Builder $query
     * @return string
     */
    public function compileSelect(Builder $query)
    {

        // If the query does not have any columns set, we'll set the columns to the
        // * character to just get all of the columns from the database. Then we
        // can build the query and concatenate all the pieces together as one.
        $original = $query->columns;

        if (is_null($query->columns)) {
            $query->columns = ['*'];
        }

        // To compile the query, we'll spin through each component of the query and
        // see if that component exists. If it does we'll just call the compiler
        // function for the component which is responsible for making the SQL.
        $sql = trim($this->concatenate(
            $this->compileComponents($query))
        );

        $query->columns = $original;

        return $sql;
    }

    /**
     * Compile a insert query into SQL
     *
     * @param Builder $query
     * @param array $values
     * @return string
     */
    public function compileInsert(Builder $query, array $values): string
    {
        $table = $this->wrapTable($query->from);

        if (! is_array(reset($values))) {
            $values = [$values];
        }

        $columns = $this->columnize(array_keys(reset($values)));

        // We need to build a list of parameter place-holders of values that are bound
        // to the query. Each insert should have the exact same amount of parameter
        // bindings so we will loop through the record and parameterize them all.
        $parameters = arr($values)->map(function ($record) {
            return '('.$this->parameterize($record).')';
        })->implode(', ');

        return "insert into $table ($columns) values $parameters";
    }

    /**
     * Compile the components necessary for a select clause.
     *
     * @param  Builder $query
     * @return array
     */
    protected function compileComponents(Builder $query)
    {
        $sql = [];

        foreach ($this->selectComponents as $component) {
            // To compile the query, we'll spin through each component of the query and
            // see if that component exists. If it does we'll just call the compiler
            // function for the component which is responsible for making the SQL.
            if (!is_null($query->$component)) {
                $method = 'compile' . ucfirst($component);

                $sql[$component] = $this->$method($query, $query->$component);
            }
        }

        return $sql;
    }

    /**
     * Compile the "select *" portion of the query.
     *
     * @param  Builder $query
     * @param  array $columns
     * @return string|null
     */
    protected function compileColumns(Builder $query, $columns)
    {
        // If the query is actually performing an aggregating select, we will let that
        // compiler handle the building of the select clauses, as it will need some
        // more syntax that is best handled by that function to keep things neat.
        if (!is_null($query->aggregate)) {
            return null;
        }

        $select = $query->distinct ? 'select distinct ' : 'select ';

        return $select . $this->columnize($columns);
    }

    /**
     * Compile the "from" portion of the query.
     *
     * @param  Builder $query
     * @param  string $table
     * @return string
     */
    protected function compileFrom(Builder $query, $table)
    {
        return 'from ' . $this->wrapTable($table);
    }

    /**
     * Compile the "where" portions of the query.
     *
     * @param  Builder  $query
     * @return string
     */
    protected function compileWheres(Builder $query)
    {
        // Each type of where clauses has its own compiler function which is responsible
        // for actually creating the where clauses SQL. This helps keep the code nice
        // and maintainable since each clause has a very small method that it uses.
        if (is_null($query->wheres)) {
            return '';
        }

        // if multiple where clauses
        $sql = "";
        foreach ($query->wheres as $where) {
            $sql .= $this->concatenateWhereClauses($query, $where);
        }

        return $sql;
    }

    protected function compileOrders(Builder $query, $orders)
    {
        $sql = "ORDER BY ";

        if(is_array($orders[0])) {
            $i = 1;
            $count = count($orders[0]);
            foreach ($orders[0] as $column) {
                $append = $count > $i ? ", " : "";
                $sql .= "`".$column."`".$append;
                $i++;
            }
        } else {
            $sql .= $orders[0];
        }

        $sql .= " ".$orders[1];

        return $sql;
    }

    /**
     * Compile the "limit" portions of the query.
     *
     * @param Builder $query
     * @param $limit
     * @return string
     */
    protected function compileLimit(Builder $query, $limit)
    {
        $sql = "LIMIT ".$limit;

        return $sql;
    }

    protected function concatenateWhereClauses(Builder $query, $wheres)
    {
        $sql = "WHERE " . $wheres[0] . $wheres[1] . "'" . $wheres[2] . "'";

        if(count($query->wheres) > 1) {
            $sql .= $wheres[3];
        }

        return $sql;
    }

    protected function compileAggregate(Builder $query, $aggregate)
    {
        $column = $this->columnize($aggregate['columns']);

        // If the query has a "distinct" constraint and we're not asking for all columns
        // we need to prepend "distinct" onto the column name so that the query takes
        // it into account when it performs the aggregating operations on the data.
        if ($query->distinct && $column !== '*') {
            $column = 'distinct '.$column;
        }

        return 'select '.$aggregate['function'].'('.$column.') as aggregate';
    }

    /**
     * Compile an exists statement into SQL.
     *
     * @param Builder $query
     * @return string
     */
    public function compileExists(Builder $query)
    {
        $select = $this->compileSelect($query);

        return "select exists($select) as {$this->wrap('exists')}";
    }

    /**
     * Concatenate an array of segments, removing empties.
     *
     * @param  array $segments
     * @return string
     */
    protected function concatenate($segments)
    {
        return implode(' ', array_filter($segments, function ($value) {
            return (string)$value !== '';
        }));
    }

    /**
     * Convert an array of column names into a delimited string.
     *
     * @param  array $columns
     * @return string
     */
    public function columnize(array $columns)
    {
        return implode(', ', array_map([$this, 'wrap'], $columns));
    }

    /**
     * Create query parameter place-holders for an array.
     *
     * @param  array   $values
     * @return string
     */
    public function parameterize(array $values)
    {
        return implode(', ', array_map([$this, 'parameter'], $values));
    }

    /**
     * Get the appropriate query parameter place-holder for a value.
     *
     * @param  mixed   $value
     * @return string
     */
    public function parameter($value)
    {
        return $this->isExpression($value) ? $this->getValue($value) : $value;
    }

    /**
     * Quote the given string literal.
     *
     * @param  string|array  $value
     * @return string
     */
    public function quoteString($value)
    {
        if (is_array($value))
            return implode(', ', array_map([$this, __FUNCTION__], $value));

        return "'$value'";
    }


    /**
     * Wrap a table in keyword identifiers.
     *
     * @param  Expression|string $table
     * @return string
     */
    public function wrapTable($table)
    {
        if (!$this->isExpression($table)) {
            return $this->wrap($this->tablePrefix . $table, true);
        }

        return $this->getValue($table);
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param  Expression|string $value
     * @param  bool $prefixAlias
     * @return string
     */
    public function wrap($value, $prefixAlias = false)
    {
        if ($this->isExpression($value)) {
            return $this->getValue($value);
        }

        // If the value being wrapped has a column alias we will need to separate out
        // the pieces so we can wrap each of the segments of the expression on it
        // own, and then joins them both back together with the "as" connector.
        if (strpos(strtolower($value), ' as ') !== false) {
            $segments = explode(' ', $value);

            if ($prefixAlias) {
                $segments[2] = $this->tablePrefix.$segments[2];
            }

            return $this->wrap($segments[0]).' as '.$this->wrapValue($segments[2]);
        }

        $wrapped = [];

        $segments = explode('.', $value);

        // If the value is not an aliased table expression, we'll just wrap it like
        // normal, so if there is more than one segment, we will wrap the first
        // segments as if it was a table and the rest as just regular values.
        foreach ($segments as $key => $segment) {
            if ($key == 0 && count($segments) > 1) {
                $wrapped[] = $this->wrapTable($segment);
            } else {
                $wrapped[] = $this->wrapValue($segment);
            }
        }

        return implode('.', $wrapped);
    }

    /**
     * Wrap a value that has an alias.
     *
     * @param  string $value
     * @param  bool $prefixAlias
     * @return string
     */
    protected function wrapAliasedValue($value, $prefixAlias = false)
    {
        $segments = preg_split('/\s+as\s+/i', $value);

        // If we are wrapping a table we need to prefix the alias with the table prefix
        // as well in order to generate proper syntax. If this is a column of course
        // no prefix is necessary. The condition will be true when from wrapTable.
        if ($prefixAlias) {
            $segments[1] = $this->tablePrefix . $segments[1];
        }

        return $this->wrap(
                $segments[0]) . ' as ' . $this->wrapValue($segments[1]
            );
    }

    /**
     * Wrap the given value segments.
     *
     * @param  array $segments
     * @return string
     */
    protected function wrapSegments($segments)
    {
        return arr($segments)->map(function ($segment, $key) use ($segments) {
            return $key == 0 && count($segments) > 1
                ? $this->wrapTable($segment)
                : $this->wrapValue($segment);
        })->implode('.');
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param  string $value
     * @return string
     */
    protected function wrapValue($value)
    {
        if ($value !== '*') {
            return '`' . str_replace('"', '""', $value) . '`';
        }

        return $value;
    }

    /**
     * Get the value of a raw expression.
     *
     * @param  Expression $expression
     * @return string
     */
    public function getValue($expression)
    {
        return $expression->getValue();
    }

    /**
     * Determine if the given value is a raw expression.
     *
     * @param  mixed $value
     * @return bool
     */
    public function isExpression($value)
    {
        return $value instanceof Expression;
    }
}
