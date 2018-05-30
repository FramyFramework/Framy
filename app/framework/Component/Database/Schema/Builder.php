<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Schema;

    /**
     * Class Builder
     * Parse the sql query.
     *
     * @package app\framework\Component\Database\Schema
     */
    class Builder
    {
        /**
         * Builds based on thr Blueprint the sql query.
         *
         * @param Blueprint $table
         * @return string
         */
        public static function createTable(Blueprint $table): string
        {
            $query = "CREATE TABLE `".$table->getTable()."`";

            // go through columns
            $query .= " (";
            foreach ($table->getColumns() as $key => $Column)
            {
                $query .= "`".$Column->name."` ";

                $query .= $Column->type;

                if(isset($Column->length))
                    $query .= "(".$Column->length.")";

                if($Column->isUnsigned)
                    $query .= " UNSIGNED";

                if($Column->isNull)
                    $query .= " NOT NULL";

                if($Column->isAutoIncrement)
                    $query .= " AUTO_INCREMENT";

                if($Column->isPrimaryKey)
                    $query .= " PRIMARY KEY";

                if(count($table->getColumns())-1 != $key)
                    $query .= ", ";
            }
            $query .= ");";

            return $query;
        }

        /**
         * @param string $name
         * @return string
         */
        public static function dropTable(string $name): string
        {
            return "DROP TABLE `".$name."`;";
        }
    }