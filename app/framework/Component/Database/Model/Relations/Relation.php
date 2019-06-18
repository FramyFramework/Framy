<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Model\Relations;

use app\framework\Component\Database\Model\Model;
use app\framework\Component\Database\Query\Builder;

abstract class Relation
{
    /**
     * The Eloquent query builder instance.
     *
     * @var Builder
     */
    protected $query;

    /**
     * The parent model instance.
     *
     * @var Model
     */
    protected $parent;

    /**
     * The related model instance.
     *
     * @var Model
     */
    protected $related;

    /**
     * Create a new relation instance.
     *
     * @param Builder $query
     * @param Model $parent
     */
    public function __construct(Builder $query, Model $parent)
    {
        $this->query   = $query;
        $this->parent  = $parent;
        $this->related = $query->getModel();
    }

}
