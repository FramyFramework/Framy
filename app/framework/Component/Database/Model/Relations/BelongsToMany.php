<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Model\Relations;


use app\framework\Component\Database\Model\Builder;
use app\framework\Component\Database\Model\Model;
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;

class BelongsToMany extends Relation
{
    /**
     * The key name of the related model.
     *
     * @var string
     */
    protected $relatedKey;

    /**
     * The key name of the parent model.
     *
     * @var string
     */
    protected $parentKey;

    /**
     * The "name" of the relationship.
     *
     * @var string|null
     */
    protected $relationName;

    /**
     * The associated key of the relation.
     *
     * @var string
     */
    protected $relatedPivotKey;

    /**
     * The foreign key of the parent model
     *
     * @var string
     */
    protected $foreignPivotKey;

    /**
     * The intermediate table for the relation.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new belongs to many relationship instance.
     *
     * @param Builder $query
     * @param Model $parent
     * @param string $table
     * @param string $foreignPivotKey
     * @param string $relatedPivotKey
     * @param string $parentKey
     * @param string $relatedKey
     * @param string $relationName
     * @throws StringObjectException
     */
    public function __construct(Builder $query, Model $parent, $table, $foreignPivotKey,
        $relatedPivotKey, $parentKey, $relatedKey, $relationName = null)
    {
        $this->parentKey       = $parentKey;
        $this->relatedKey      = $relatedKey;
        $this->relationName    = $relationName;
        $this->relatedPivotKey = $relatedPivotKey;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->table           = $this->resolveTableName($table);

        parent::__construct($query, $parent);
    }

    /**
     * Attempt to resolve the intermediate table name from the given string.
     *
     * @param string $table
     * @return string
     * @throws StringObjectException
     */
    protected function resolveTableName($table)
    {
        if (! Str($table)->contains('\\') || ! class_exists($table)) {
            return $table;
        }

        $model = new $table;

        if (! $model instanceof Model) {
            return $table;
        }

        if ($model instanceof Pivot) {
            $this->using($table);
        }

        return $model->getTable();
    }


    public function addConstraints()
    {
        // TODO: Implement addConstraints() method.
    }
}