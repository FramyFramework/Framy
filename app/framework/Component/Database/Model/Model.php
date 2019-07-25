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
use app\framework\Component\Database\Model\Concerns\HasRelationships;
use app\framework\Component\Database\Query\Builder as QueryBuilder;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;
use app\framework\Component\StdLib\StdObject\DateTimeObject\DateTimeObject;
use app\framework\Component\StdLib\StdObject\StringObject\StringObject;
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;
use ArrayAccess;
use JsonSerializable;

/**
 * @package app\framework\Component\Database\Model
 */
class Model implements ArrayAccess, JsonSerializable
{
    use HasRelationships;

    /**
     * The connection name for the model.
     *
     * @var string $connection
     */
    protected $connection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * Rather or not the model has been booted
     *
     * @var bool
     */
    protected static $isBooted = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    protected $incrementing = true;

    /**
     * @var bool
     */
    protected $timestamps = true;

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The model attribute's original state.
     *
     * @var array
     */
    protected $original = [];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**use app\framework\Component\Database\DB;

     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Model constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->bootIfNotBooted();

        $this->syncOriginal();

        $this->fill($attributes);
    }

    protected static function boot()
    {
        // todo: something should happen here
    }

    /**
     * Check if the model needs to be booted, and if so boot
     */
    public function bootIfNotBooted()
    {
        if (! self::$isBooted) {
            self::boot();

            self::$isBooted = true;
        }
    }

    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }

    /**
     * Sync a single original attribute with its current value.
     *
     * @param  string  $attribute
     * @return $this
     */
    public function syncOriginalAttribute($attribute)
    {
        $this->original[$attribute] = $this->attributes[$attribute];

        return $this;
    }

    /**
     * primaryKey getter
     *
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * primaryKey setter
     *
     * @param string $primaryKey
     */
    public function setPrimaryKey(string $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return str(class_basename($this).'_'.$this->getPrimaryKey())->snakeCase()->val();
    }

    /**
     * @return bool
     */
    public function isIncrementing(): bool
    {
        return $this->incrementing;
    }

    /**
     * @param bool $incrementing
     */
    public function setIncrementing(bool $incrementing): void
    {
        $this->incrementing = $incrementing;
    }

    /**
     * Save the model to the database.
     *
     * @return bool
     * @throws ConnectionNotConfiguredException
     */
    public function save()
    {
        // If the model already exists in the database we can just update our record
        // that is already in this database using the current IDs in this "where"
        // clause to only update this model. Otherwise, we'll just insert them.
        if ($this->exists) {
            $saved = $this->performUpdate($this->newQuery());
        }

        // If the model is brand new, we'll insert it into our database and set the
        // ID attribute on the model to the value of the newly inserted row's ID
        // which is typically an auto-increment value managed by the database.
        else {
            $saved = $this->performInsert($this->newQuery());
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done. We will call the "saved" method here to run any actions
        // we need to happen after a model gets successfully saved right here.
        if ($saved) {
            $this->finishSave();
        }

        return $saved;
    }

    /**
     * Get number of entries in table
     */
    public function count()
    {
        $instance       = new static();
        $result         = $instance->newQuery()->count();

        return $result;
    }

    /**
     * Fill attributes by giving array.
     *
     * @param array $attributes
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $values) {
            $this->setAttribute($key, $values);
        }
    }

    /**
     * Deletes the model
     *
     * @return int Number of effected rows
     * @throws StringObjectException
     * @throws ConnectionNotConfiguredException
     */
    public function delete()
    {
        return $this->newQuery()
            ->wherePrimaryKey(
                $this->offsetGet($this->getPrimaryKey())
            )->delete();
    }

    /**
     * Receive all model
     *
     * @param array $columns
     * @return mixed
     */
    public static function all(array $columns = ['*'])
    {
        $instance = new static();
        /** @var ArrayObject $result */
        $result   = $instance->newQuery()->get($columns);

        $result->map(function ($item) {
            $item->exists = true;
        });

        return $result;
    }

    /**
     * @param array|int $id
     * @return ArrayObject|Model|null
     */
    public static function find($id)
    {
        $instance = new static();
        $result   = $instance->newQuery()->find($id);

        if (! is_null($result)) {
            $result->exists = true;
        }

        return $result;
    }

    public static function findOrFail($id)
    {
        $instance       = new static();
        $result         = $instance->newQuery()->findOrFail($id);
        $result->exists = true;

        return $result;
    }

    public static function first()
    {
        // TODO: implement
    }

    public static function latest()
    {
        // TODO: implement
    }

    /**
     * Remove an selection of Models
     *
     * @param $id array|int
     * @return int Number of effected rows
     */
    public static function remove($id)
    {
        $instance = new static();

        if (! is_array($id)) {
            $id = [$id];
        }

        return $instance->newQuery()->remove($id);
    }

    /**
     * @param               $column
     * @param  string       $operator
     * @param               $value
     * @param  string       $boolean
     * @return QueryBuilder
     */
    public static function where($column, $operator = "=", $value = null, $boolean = 'and')
    {
        $instance = new static();
        $result   = $instance->newQuery()->where($column, $operator, $value, $boolean);

        return $result;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (! $key) {
            return;
        }

        $attr = $this->getAttributes()[$key];

        if (isset($attr)) {
            return $attr;
        }

        return;
    }

    /**
     * Get connection
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns table name. Extracts table name if not yet set.
     *
     * @return mixed
     * @throws StringObjectException
     */
    public function getTable()
    {
        /** @var String|StringObject $table */
        $table = $this->table;

        if ($table === null) {
            $table = str(str(get_class($this))->explode("\\")->last());
            $table->snakeCase();

            $table->append("s");
        }

        return $this->table = is_string($table) ? $table : $table->val();
    }

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        $this->{static::CREATED_AT} = $value;

        return $this;
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        $this->{static::UPDATED_AT} = $value;

        return $this;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }

    /**
     * Perform a model insert operation.
     *
     * @param Builder $query
     * @return bool
     */
    protected function performInsert(Builder $query)
    {
        // First we'll need to create a fresh query instance and touch the creation and
        // update timestamps on this model, which are maintained by us for developer
        // convenience. After, we will just continue saving these model instances.
        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }

        // If the model has an incrementing key, we can use the "insertGetId" method on
        // the query builder, which will give us back the final inserted ID for this
        // table from the database. Not all tables have to be incrementing though.
        $attributes = $this->getAttributes();

        if ($this->isIncrementing()) {
            $this->insertAndSetId($query, $attributes);
        }

        // If the table isn't incrementing we'll simply insert these attributes as they
        // are. These attribute arrays must contain an "id" column previously placed
        // there by the developer as the manually determined key for these models.
        else {
            if (empty($attributes)) {
                return true;
            }

            $query->insert($attributes);
        }

        // We will go ahead and set the exists property to true, so that it is set when
        // the created event is fired, just in case the developer tries to update it
        // during the event. This will allow them to do so and run an update here.
        $this->exists = true;

        return true;
    }

    /**
     * Performing an model update operation.
     *
     * @param  Builder $query
     * @return bool
     */
    protected function performUpdate(Builder $query)
    {
        // We will need to set the updated at stamp to current time
        if ($this->usesTimestamps()) {
            $this->setUpdatedAt($this->freshTimestamp());
        }

        // Get the changed attributes which shall then be updated
        $attributes = array_diff($this->getAttributes(), $this->original);

        // casting to bool because if it performed an action
        // we will have $result = true and false otherwise
        $result = (bool) $query->where(
            $this->getPrimaryKey(),
            '=',
            $this->offsetGet($this->getPrimaryKey())
        )->update($attributes);

        $this->syncOriginal();

        return $result;
    }
    
    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }

    /**
     * @inheritDoc
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Get a new Query Builder
     *
     * @return Builder
     * @throws ConnectionNotConfiguredException
     */
    public function newQuery()
    {
        return new Builder(
            $this
        );
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return QueryBuilder
     * @throws ConnectionNotConfiguredException
     */
    protected function newBaseQueryBuilder()
    {
        $conn = ConnectionFactory::getInstance()->get(
            $this->getConnection()
        );

        return new QueryBuilder($conn);
    }

    /**
     * Update the creation and update timestamps.
     *
     * @return void
     */
    protected function updateTimestamps()
    {
        $time = $this->freshTimestamp();

        if (! is_null(static::UPDATED_AT) ) {
            $this->setUpdatedAt($time);
        }

        if (! is_null(static::CREATED_AT)) {
            $this->setCreatedAt($time);
        }
    }

    /**
     * Insert the given attributes and set the ID on the model.
     *
     * @param  Builder  $query
     * @param  array  $attributes
     * @return void
     */
    protected function insertAndSetId(Builder $query, $attributes)
    {
        $id = $query->insertGetId($attributes, $keyName = $this->getPrimaryKey());

        $this->setAttribute($keyName, $id);
    }

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps()
    {
        return $this->timestamps;
    }

    /**
     * Returns current date time
     *
     * @return string
     */
    protected function freshTimestamp()
    {
        return (new DateTimeObject())->val();
    }

    /**
     * Perform any actions that are necessary after the model is saved.
     */
    protected function finishSave()
    {
        // fire Event?

        $this->syncOriginal();
    }
}
