<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Connection;

    class Driver
    {
        const MariaDB = "mariadb";
        const MySql = "mysql";
        const PgSql = "pgsql";
        const SyBase = "sybase";
        const Oracle = "oracle";
        const MsSql = "mssql";
        const SqLite = "sqlite";
        const DbLib  = "dblib";

        public static function mysql(array $config)
        {
            $dsn = "";

            $attr = [
                'driver' => Driver::MySql,
                'dbname' => $config['database']
            ];

            if (isset($config['socket'])) {
                $attr['unix_socket'] = $config['socket'];
            } else {
                $attr['host'] = $config['host'];

                if (isset($config["port"]))
                    $attr['port'] = $config["port"];
            }

            self::parseDsn($dsn, $attr);
            return $dsn;
        }

        public static function pgsql(array $config)
        {
            $dsn = "";

            $attr = [
                'driver' => Driver::PgSql,
                'host' => $config['host'],
                'dbname' => $config['database']
            ];

            if (isset($config["port"]))
                $attr['port'] = $config["port"];

            self::parseDsn($dsn, $attr);
            return $dsn;
        }

        public static function sybase(array $config)
        {
            $dsn = "";

            $attr = [
                'driver' => Driver::DbLib,
                'host' => $config['host'],
                'dbname' => $config['database']
            ];

            if (isset($config["port"]))
                $attr['port'] = $config["port"];

            self::parseDsn($dsn, $attr);
            return $dsn;
        }

        public static function oracle(array $config)
        {
            $dsn = "";

            $attr = [
                'driver' => 'oci',
                'dbname' => $config['server'] ?
                    '//' . $config['server'] . (isset($config["port"]) ? ':' . $config["port"] : ':1521') . '/' . $config['database'] :
                    $config['database']
            ];

            if (isset($config['charset']))
                $attr['charset'] = $config['charset'];

            self::parseDsn($dsn, $attr);
            return $dsn;
        }

        public static function mssql(array $config)
        {
            $dsn = "";

            if (strstr(PHP_OS, 'WIN')) {
                $attr = [
                    'driver' => 'sqlsrv',
                    'server' => $config['server'],
                    'database' => $config['database']
                ];
            } else {
                $attr = [
                    'driver' => 'dblib',
                    'host' => $config['host'],
                    'dbname' => $config['database']
                ];
            }

            if (isset($config["port"]))
                $attr['port'] = $config["port"];

            self::parseDsn($dsn, $attr);
            return $dsn;
        }

        /*public static function sqlite(array $config)
        {
            $dsn = "";

            //self::parseDsn($dsn, $attr);
            return $dsn;
        }*/

        private static function parseDsn(&$dsn, $attr)
        {
            $driver = $attr['driver'];
            unset($attr['driver']);

            $stack = [];

            foreach ($attr as $key => $value) {
                if (is_int($key))
                    $stack[] = $value;
                else
                    $stack[] = $key . '=' . $value;
            }

            $dsn = $driver . ':' . implode($stack, ';');
        }
    }