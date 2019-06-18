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
use app\framework\Component\Database\Model\Relations\HasOne;
use app\framework\Component\Database\Query\Builder;

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
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     * @return HasOne
     * @throws ConnectionNotConfiguredException
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        // create a instance of $related
        /** @var Model $instance*/
        $instance = new $related;

        // get foreignKey
        // ger localKey

        // create new HasOne instance and return
        return $this->newHasOne($instance->newQuery(), $this, $foreignKey, $localKey);
    }

    /**
     * Instantiate a new HasOne relationship.
     */
    protected function newHasOne(Builder $builder, $parent, $foreignKey, $localKey)
    {
        return new HasOne($builder, $parent, $foreignKey, $localKey);
    }
}
