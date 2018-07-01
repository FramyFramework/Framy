<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Query;
    use app\framework\Component\Database\Connection\Connection;

    /**
     * Class Builder
     * Build Query based on grammar.
     *
     * @package app\framework\Component\Database\Query
     */
    class Builder
    {
        /**
         * @var Connection
         */
        private $connection;

        /**
         * Builder constructor.
         *
         * @param Connection $connection
         */
        public function __construct(Connection $connection)
        {
            $this->connection = $connection;
        }
    }