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
use PDO;

/**
 * Class ConnectionFactory
 * Created to be an convenient way of creating connections
 * based on configurations. But will be modified to hold the
 * instances of the connections as well, so for every connection
 * used only one instance of it will exist.
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
     * @var Connection[]
     */
    private $connections = [];

    /**
     * Contains the connection configuration information's
     *
     * @var array
     */
    private $configuration = [];

    /**
     * @param string|null $name
     * @return Connection
     * @throws ConnectionNotConfiguredException
     */
    public function get(string $name = null): Connection
    {
        // check if connection with $name was already instantiated if yes return
        if(isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        // otherwise create and save in $connections
        return $this->make($name);
    }

    /**
     * Establish a PDO connection based on the configuration.
     * Set up default connection or the one defined and save in $connections
     *
     * @param $name String Name of the connection. If null use default.
     * @return Connection
     * @throws ConnectionNotConfiguredException
     */
    public function make(string $name = null): Connection
    {
        $this->extractConfig();

        $connection = $this->createSingleConnection(
            $this->getConnectionName($name)
        );

        $this->connections[$name] = $connection;

        return $connection;
    }

    /**
     *  Sets the $configuration value
     */
    private function extractConfig()
    {
        $connByConfig = Config::getInstance()->get("connections");
        $config = [];

        foreach ($connByConfig as $name => $con) {
            $config[$name] = $this->parseConfig($con);
        }

        $this->configuration = $config;
    }

    /**
     * @param string|null $name
     * @return int|string|null
     * @throws ConnectionNotConfiguredException
     */
    private function getConnectionName(string $name = null)
    {
        $config = $this->configuration;

        if (is_null($name)) {
            reset($config);
            return key($config);
        }

        if (isset($config[$name])) {
            return $name;
        } else {
            // if conf not found Exception
            throw new ConnectionNotConfiguredException("Connection $name is not Configured");
        }
    }
    
    /**
     * To be sure that the config array is as he is supposed to be.
     *
     * @param array $config
     * @return array the connection config
     */
    private function parseConfig(array $config): array
    {
        // make sure that the default config elements exist to prevent an undefined index warning
        foreach(self::defaultConfigElements as $configElement) {
            if(!array_key_exists($configElement, $config)) {
                $config[$configElement];
            }
        }

        return $config;
    }

    /**
     * Create a single database connection instance.
     *
     * @param string $name
     * @return Connection
     */
    private function createSingleConnection(string $name): Connection
    {
        $config = $this->configuration;

        $Pdo = $this->createPdoInstance($config[$name]);

        return $this->createConnection($Pdo, $config[$name]['database'], $name, $config);
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

        return new PDO($dsn, $config['username'], $config['password']);
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
