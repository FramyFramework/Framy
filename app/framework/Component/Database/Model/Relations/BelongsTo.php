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

/**
 * Class BelongsTo
 *
 * @package app\framework\Component\Database\Model\Relations
 */
class BelongsTo extends Relation
{
    /**
     * The child model instance of the relation.
     */
    protected $child;
    
    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The associated key on the parent model.
     *
     * @var string
     */
    protected $ownerKey;

    /**
     * The name of the relationship.
     *
     * @var string
     */
    protected $relationName;

    /**
     * Create a new belongs to relationship instance.
     *
     * @param Builder $query
     * @param Model $child
     * @param $foreignKey
     * @param $ownerKey
     * @param $relationName
     */
    public function __construct(Builder $query, Model $child, $foreignKey, $ownerKey, $relationName)
    {
        $this->foreignKey   = $foreignKey;
        $this->ownerKey     = $ownerKey;
        $this->relationName = $relationName;

        // In the underlying base relationship class, this variable is referred to as
        // the "parent" since most relationships are not inversed. But, since this
        // one is we will create a "child" variable for much better readability.
        $this->child = $child;

        parent::__construct($query, $child);
    }
    
    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     * @throws StringObjectException
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            // For belongs to relationships, which are essentially the inverse of has one
            // or has many relationships, we need to actually query on the primary key
            // of the related models matching on the foreign key that's on a parent.
            $table = $this->related->getTable();
            $this->query->where($table.'.'.$this->ownerKey, '=', $this->child->{$this->foreignKey});
        }
    }
}
