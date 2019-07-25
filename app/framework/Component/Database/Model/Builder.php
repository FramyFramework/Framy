<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Model;

use app\framework\Component\Database\Connection\ConnectionFactory;
use app\framework\Component\Database\Connection\ConnectionNotConfiguredException;
use app\framework\Component\Database\Query\Builder as QueryBuilder;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;

/**
 * Model Query Builder
 * Is responsible for building the queries used by the model.
 * It is another layer between the Model and The QueryBuilder
 * providing useful stuff.
 *
 * @package app\framework\Component\Database\Model
 */
class Builder extends QueryBuilder
{
    /**
     * The Model being queried.
     *
     * @var Model
     */
    protected $model;

    /**
     * Builder constructor.
     *
     * @param $model
     * @throws ConnectionNotConfiguredException
     * @throws StringObjectException
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        parent::__construct(
            ConnectionFactory::getInstance()->get(
                $model->getConnection()
            )
        );

        $this->fromTable();
    }

    /**
     * Getter for model
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return QueryBuilder
     * @throws StringObjectException
     */
    private function fromTable()
    {
        return $this->from($this->model->getTable());
    }

    /**
     * @param $id
     * @return Model|ArrayObject|null
     * @throws StringObjectException
     */
    public function find($id)
    {
        $builder = $this->from($this->model->getTable());

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
        $builder = $this->from($this->model->getTable());

        foreach ($ids as $item) {
            $builder->orWhere($this->model->getPrimaryKey(), "=", $item);
        }

        return $builder->delete();
    }
}
