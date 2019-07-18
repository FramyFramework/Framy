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
use app\framework\Component\Database\Model\Relations\BelongsToMany;
use app\framework\Component\Database\Model\Relations\HasMany;
use app\framework\Component\Database\Model\Relations\HasOne;
use app\framework\Component\Database\Model\Builder;
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;

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
        $instance = $this->instantiateRelated($related);

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
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     * @return HasMany
     * @throws ConnectionNotConfiguredException
     * @throws StringObjectException
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->instantiateRelated($related);

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
        $related = $this->instantiateRelated($related);

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

    /**
     * @param $related
     * @param null $table
     * @param null $foreignPivotKey
     * @param null $relatedPivotKey
     * @param null $parentKey
     * @param null $relatedKey
     * @param null $relation
     * @return BelongsToMany
     * @throws ConnectionNotConfiguredException
     * @throws StringObjectException
     */
    public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
                                  $parentKey = null, $relatedKey = null, $relation = null)
    {
        /** @var Model $related */
        $related = $this->instantiateRelated($related);


        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();
        $relatedPivotKey = $relatedPivotKey ?: $related->getForeignKey();

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        return $this->newBelongsToMany(
            $related->newQuery(), $this, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey ?: $this->getPrimaryKey(),
            $relatedKey ?: $related->getPrimaryKey(), $relation
        );

    }

    /**
     * Instantiate a new BelongsToMany relationship.
     *
     * @param Builder $query
     * @param Model $parent
     * @param string $table
     * @param string $foreignPivotKey
     * @param string $relatedPivotKey
     * @param string $parentKey
     * @param string $relatedKey
     * @param string $relationName
     * @return BelongsToMany
     * @throws StringObjectException
     */
    protected function newBelongsToMany(Builder $query, Model $parent, $table, $foreignPivotKey, $relatedPivotKey,
                                        $parentKey, $relatedKey, $relationName = null)
    {
        return new BelongsToMany($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }

    /**
     * Creates an instance of $related
     *
     * @param $related
     * @return Model
     */
    private function instantiateRelated($related)
    {
        return new $related;
    }


    /**
     * Get the joining table name for a many-to-many relation.
     *
     * @param  Model $related
     * @return string
     */
    public function joiningTable($related)
    {
        // The joining table name, by convention, is simply the snake cased models
        // sorted alphabetically and concatenated with an underscore, so we can
        // just sort the models and join them together to get the table name.
        $segments = [
            $related ? $related->joiningTableSegment()
                : Str(class_basename($related))->snakeCase()->val(),
            $this->joiningTableSegment(),
        ];

        // Now that we have the model names in an array we can just sort them and
        // use the implode function to join them together with an underscores,
        // which is typically used by convention within the database system.
        sort($segments);

        return strtolower(implode('_', $segments));
    }

    /**
     * Get this model's half of the intermediate table name for belongsToMany relationships.
     *
     * @return string
     */
    public function joiningTableSegment()
    {
        return Str(class_basename($this))->snakeCase()->val();
    }
}
