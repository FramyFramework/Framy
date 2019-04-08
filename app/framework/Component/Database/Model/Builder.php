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
        'insert', 'insertGetId', 'getBindings', 'toSql',
        'exists', 'count', 'min', 'max', 'avg', 'sum', 'getConnection',
    ];

    /**
     * @inheritDoc
     */
    public function __construct(QueryBulder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
}
