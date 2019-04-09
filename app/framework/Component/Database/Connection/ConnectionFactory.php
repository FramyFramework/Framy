<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Connection;

use app\framework\Component\Config\Config;
use app\framework\Component\StdLib\SingletonTrait;
use Exception;
use PDO;
use PDOException;

/**
 * Class ConnectionFactory
 * Created to be an convenient way of creating connections
 * based on configurations. But will be modified to hold the
 * instances of the connections as well, so for every connection
 * used only one instance of it will exist.
 *
 * TODO: do the thing explained above!
 *
 * @package app\framework\Component\Database\Connection
 */
class ConnectionFactory
{
    use SingletonTrait;

    const defaultConfigElements = [
        'driver',
        'host',
        'port',
        'database',
        'username',
        'password',
        'charset',
        'collation',
        'prefix',
        'strict',
        'engine',
    ];

    /**
     * Establish a PDO connection based on the configuration.
     * Set up default connection or the one defined
     * @param $name String Name of the connection. If null use default.
     * @return Connection
     */
    public function make(string $name = null): Connection
    {
        $connByConfig = Config::getInstance()->get("connections");
        try {
            if(sizeof($connByConfig ) == 1 && $name == null) {
                reset($connByConfig);
                $config = $this->parseConfig($connByConfig, key($connByConfig));
                $name = key($connByConfig);
            } else {
                $config = $this->parseConfig($connByConfig, $name);
            }

            return $this->createSingleConnection($config, $name);
        } catch (Exception $e) {
            handle($e);
        }
    }

    /**
     * To be sure that the config array is as he is supposed to be.
     *
     * @param array $config
     * @param string $name
     * @throws Exception If config does not exist.
     * @return array the connection config
     */
    private function parseConfig(array $config, string $name): array
    {
        if(isset($config[$name]))
            $connection = $config[$name];
        else
            throw new Exception("Config " .$name. " doesn't exist.");

        // make sure that the default config elements exist to prevent an undefined index warning
        foreach(self::defaultConfigElements as $configElement) {
            if(!array_key_exists($configElement, $connection))
                $connection[$configElement];
        }

        return $connection;
    }

    /**
     * Create a single database connection instance.
     *
     * @param  array  $config
     * @param string $name
     * @return Connection
     */
    private function createSingleConnection(array $config, string $name = ''): Connection
    {
        $Pdo = $this->createPdoInstance($config);

        return $this->createConnection($Pdo, $config['database'], $name, $config);
    }

    /**
     * @param array $config
     * @return PDO
     */
    private function createPdoInstance(array $config)
    {
        $dsn = "";

        switch ($config['driver']) {
            case Driver::MariaDB:
            case Driver::MySql:
                $dsn = Driver::mysql($config);
                break;

            case Driver::PgSql:
                $dsn = Driver::pgsql($config);
                break;

            case Driver::SyBase:
                $dsn = Driver::sybase($config);
                break;

            case Driver::Oracle:
                $dsn = Driver::oracle($config);
                break;

            case Driver::MsSql:
                $dsn = Driver::mssql($config);
                break;

            //case 'sqlite':
            //    $this->pdo = new PDO('sqlite:' . $config['database_file'], null, null, $this->option);
            //    break;
        }

        try {
            return new PDO($dsn, $config['username'], $config['password']);
        } catch (PDOException $e) {
            handle($e);
        }
    }

    /**
     * @param PDO $Pdo
     * @param string $database
     * @param string $name
     * @param array $config
     * @return Connection
     */
    private function createConnection(PDO $Pdo, $database = "", string $name = '', array $config = [])
    {
        return new Connection($Pdo, $database, $name, $config);
    }
}
