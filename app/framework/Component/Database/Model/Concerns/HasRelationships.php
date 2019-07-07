<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Model\Concerns;

use app\framework\Component\Database\Connection\ConnectionNotConfiguredException;
use app\framework\Component\Database\Model\Model;
use app\framework\Component\Database\Model\Relations\BelongsTo;
use app\framework\Component\Database\Model\Relations\HasMany;
use app\framework\Component\Database\Model\Relations\HasOne;
use app\framework\Component\Database\Model\Builder;

/**
 * Trait HasRelationships
 * Contains the functionality so Model can have relationships wih each other
 *
 * @package app\framework\Component\Database\Model\Concerns
 */
trait HasRelationships
{
    /**
     * The loaded relationships for the model
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Define a one-to-one relation
     *
     * @param string $related    The Model it shall relate to
     * @param string $foreignKey The
     * @param string $localKey
     * @return HasOne
     * @throws ConnectionNotConfiguredException
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        // create a instance of $related
        /** @var Model $instance*/
        $instance = new $related;

        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey   = $localKey   ?: $this->getPrimaryKey();

        // create new HasOne instance and return
        return $this->newHasOne($instance->newQuery(), $this, $foreignKey, $localKey);
    }

    /**
     * Instantiate a new HasOne relationship.
     *
     * @param Builder $builder
     * @param $parent
     * @param $foreignKey
     * @param $localKey
     * @return HasOne
     */
    protected function newHasOne(Builder $builder, $parent, $foreignKey, $localKey)
    {
        return new HasOne($builder, $parent, $foreignKey, $localKey);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = new $related;

        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey   = $localKey   ?: $this->getPrimaryKey();

        return $this->newHasMany(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey
        );
    }

    /**
     * Instantiate a new HasMany relationship.
     *
     * @param  Builder  $query
     * @param  Model  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return HasMany
     */
    protected function newHasMany(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return new HasMany($query, $parent, $foreignKey, $localKey);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $ownerKey
     * @return BelongsTo
     * @throws ConnectionNotConfiguredException
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null)
    {
        /** @var Model $related */
        $related = new $related;

        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $ownerKey   = $ownerKey   ?: $this->getPrimaryKey();

        return $this->newBelongsTo($related->newQuery(), $this, $foreignKey, $ownerKey, $related);
    }

    /**
     * @param Builder $query
     * @param Model $child
     * @param $foreignKey
     * @param $ownerKey
     * @param $relation
     * @return BelongsTo
     */
    protected function newBelongsTo(Builder $query, Model $child, $foreignKey, $ownerKey, $relation)
    {
        return new BelongsTo($query, $child, $foreignKey, $ownerKey, $relation);
    }
}
