<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Model;

use app\framework\Component\Database\Query\Builder as QueryBulder;

/**
 * Model Query Builder
 * Is responsible for building the queries used by the model.
 * It is another layer between the Model and The QueryBuilder
 * providing useful stuff.
 *
 * @package app\framework\Component\Database\Model
 */
class Builder
{
    /**
     * The base query builder instance.
     *
     * @var QueryBulder
     */
    protected $queryBuilder;

    /**
     * The Model being queried.
     *
     * @var Model
     */
    protected $model;

    /**
     * The methods that should be returned from query builder.
     *
     * @var array
     */
    protected $passThru = [
        'get', 'where',
        'insert', 'getBindings', 'toSql',
        'exists', 'count', 'min', 'max', 'avg', 'sum', 'getConnection',
    ];

    /**
     * Builder constructor.
     *
     * @param QueryBulder $queryBuilder
     * @param $model
     */
    public function __construct(QueryBulder $queryBuilder, $model)
    {
        $this->queryBuilder = $queryBuilder;
        $this->model        = $model;
    }

    /**
     * QueryBuilder getter
     *
     * @return QueryBulder
     */
    public function getQuery()
    {
        return $this->queryBuilder;
    }

    /**
     * Get a base query builder instance.
     *
     * @return QueryBulder
     */
    public function toBase()
    {
        return $this->getQuery();
    }

    /**
     * @return QueryBulder
     */
    private function fromTable()
    {
        return $this->toBase()
            ->from($this->model->getTable())
            ->get($columns);
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param $name
     * @param $arguments
     * @return Builder|mixed
     */
    public function __call($name, $arguments)
    {
        if(in_array($name, $this->passThru)) {
            return call_user_func_array([$this->fromTable(), $name], $arguments);
        }
    }
}
