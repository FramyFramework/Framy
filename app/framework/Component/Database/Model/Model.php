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

    /**
     *
     *
     * @package app\framework\Component\Database\Model
     */
    abstract class Model
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
    }