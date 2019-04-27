<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Model;

use app\framework\Component\Database\Query\Builder as QueryBuilder;
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;

/**
 * Model Query Builder
 * Is responsible for building the queries used by the model.
 * It is another layer between the Model and The QueryBuilder
 * providing useful stuff.
 *
 * @method QueryBuilder where($column, $operator = "=", $value = null, $boolean = 'and')
 * @package app\framework\Component\Database\Model
 */
class Builder
{
    /**
     * The base query builder instance.
     *
     * @var QueryBuilder
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
        'insert', 'insertGetId', 'getBindings', 'toSql',
        'exists', 'count', 'min', 'max', 'avg', 'sum', 'getConnection',
        'first'
    ];

    /**
     * Builder constructor.
     *
     * @param QueryBuilder $queryBuilder
     * @param $model
     */
    public function __construct(QueryBuilder $queryBuilder, $model)
    {
        $this->queryBuilder = $queryBuilder;
        $this->model        = $model;
    }

    /**
     * QueryBuilder getter
     *
     * @return QueryBuilder
     */
    public function getQuery()
    {
        return $this->queryBuilder;
    }

    /**
     * Get a base query builder instance.
     *
     * @return QueryBuilder
     */
    public function toBase()
    {
        return $this->getQuery();
    }

    /**
     * @return QueryBuilder
     * @throws StringObjectException
     */
    private function fromTable()
    {
        return $this->toBase()
            ->from($this->model->getTable());
    }

    /**
     * @param $id
     * @return Model|array|null
     * @throws StringObjectException
     */
    public function find($id)
    {
        $builder = $this->toBase()
            ->from($this->model->getTable());

        if (!is_array($id)) {
            $id = [$id];
        }

        foreach ($id as $item) {
            $builder->orWhere($this->model->getPrimaryKey(), "=", $item);
        }

        if (sizeof($id) === 1) {
            $result = $builder->first();
        } else {
            $result = $builder->get();
        }

        return $result;
    }

    /**
     * Add where constrain where {primarykey} = $value
     *
     * @param $value
     * @return QueryBuilder
     * @throws StringObjectException
     */
    public function wherePrimaryKey($value)
    {
        return $this->fromTable()->Where($this->model->getPrimaryKey(), "=", $value);
    }

    /**
     * @param $id
     * @return Model|array|null
     * @throws ModelNotFoundException
     * @throws StringObjectException
     */
    public function findOrFail($id)
    {
        if (!is_null($res = $this->find($id))) {
            return $res;
        }

        throw new ModelNotFoundException("Model `".$this->model->getTable()."` not found.");
    }

    /**
     * Remove an selection of Models
     *
     * @param array $ids
     * @return int Number of effected rows
     * @throws StringObjectException
     */
    public function remove(array $ids)
    {
        $builder = $this->toBase()
            ->from($this->model->getTable());

        foreach ($ids as $item) {
            $builder->orWhere($this->model->getPrimaryKey(), "=", $item);
        }

        return $builder->delete();
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param $name
     * @param $arguments
     * @return Builder|mixed
     * @throws StringObjectException
     */
    public function __call($name, $arguments)
    {
        if(in_array($name, $this->passThru)) {
            return call_user_func_array([$this->fromTable(), $name], $arguments);
        }
    }
}
