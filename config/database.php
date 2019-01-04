<?php

    return [

        /*
         |----------------------------------------
         | Database Connections
         |----------------------------------------
         |
         | Here you can configure your connections. The key of the array
         | in the example 'mysql' is the name of the connection.
         |
         */

        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'port' => 3306,
                'database' => 'mrfibunacci',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ],
        ]
    ];