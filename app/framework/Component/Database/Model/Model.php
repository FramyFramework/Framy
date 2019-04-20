<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Model;

use app\framework\Component\Database\Connection\Connection;
use app\framework\Component\Database\Connection\ConnectionFactory;
use app\framework\Component\Database\Connection\ConnectionNotConfiguredException;
use app\framework\Component\Database\Query\Builder as QueryBuilder;
use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;
use app\framework\Component\StdLib\StdObject\StringObject\StringObject;
use app\framework\Component\StdLib\StdObject\StringObject\StringObjectException;
use ArrayAccess;
use JsonSerializable;

/**
 * @package app\framework\Component\Database\Model
 */
class Model implements ArrayAccess, JsonSerializable
{
    /**
     * The connection name for the model.
     *
     * @var $connection
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
    protected $original;

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
     * to save the this model to the database
     */
    public function save()
    {}

    /**
     * Get number of entries in table
     */
    public function count()
    {
        $instance = new static();
        $instance->newQuery()->count();
    }

    /**
     * selects entries of table and return array of Models filled with data.
     */
    public function get()
    {}

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $values) {
            $this->setAttribute($key, $values);
        }
    }

    public function fillData(array $data)
    {
        foreach ($data as $key => $datum) {
            $this->$key = $datum;
        }
    }

    public static function all(array $columns = ['*'])
    {
        $instance = new static;

        return $instance->newQuery()->get($columns);
    }

    /**
     * @param array|int $id
     * @return ArrayObject|Model|null
     */
    public static function find($id)
    {
        $instance = new static();

        return $instance->newQuery()->find($id);
    }

    public static function findOrFail($id)
    {
        $instance = new static();

        return $instance->newQuery()->findOrFail($id);
    }

    public static function first()
    {
    }

    public static function latest()
    {
    }

    /**
     * @param $column
     * @param string $operator
     * @param null $value
     * @param string $boolean
     * @return QueryBuilder
     */
    public static function where($column, $operator = "=", $value = null, $boolean = 'and')
    {
        $instance = new static();

        return $instance->newQuery()->where($column, $operator, $value, $boolean);
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

    /**
     * Get connection
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
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

        return $this->table = is_string($table) ? $table->val() : $table;
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
     * Get a new Query Builder
     *
     * @return Builder
     * @throws ConnectionNotConfiguredException
     */
    public function newQuery()
    {
        return new Builder(
            $this->newBaseQueryBuilder(),
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
}
