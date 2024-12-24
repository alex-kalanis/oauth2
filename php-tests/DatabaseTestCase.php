<?php

namespace Tests;


use Nette\Bootstrap\Configurator;
use Nette\Database\Connection;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Tester\Environment;


/**
 * DatabaseTestCase
 * @package Tests
 */
abstract class DatabaseTestCase extends TestCase
{

    protected Connection $connection;
    protected Explorer $dbExplorer;
    private Container $container;

    public function __construct()
    {
        $configurator = new Configurator();

        $config = $configurator->setTempDirectory(TEMP_DIR);
        $config->addStaticParameters([
            'OAUTH2_MYSQL_DB_HOST' => strval(getenv('OAUTH2_MYSQL_DB_HOST')),
            'OAUTH2_MYSQL_DB_NAME' => strval(getenv('OAUTH2_MYSQL_DB_NAME')),
            'OAUTH2_MYSQL_DB_USER' => strval(getenv('OAUTH2_MYSQL_DB_USER')),
            'OAUTH2_MYSQL_DB_PASS' => strval(getenv('OAUTH2_MYSQL_DB_PASS')),
        ]);
        $config->addConfig(__DIR__ . DIRECTORY_SEPARATOR . 'config.neon');

        $this->container = $config->createContainer();
    }

    /**
     * Setup database
     */
    protected function setUp(): void
    {
        parent::setUp();
        Environment::lock('db', dirname(TEMP_DIR));

        $this->connection = $this->getConnection();
        $this->dbExplorer = $this->getDbExplorer();

//        $this->connection->beginTransaction();
        $this->emptyDatabase();
        $this->createDatabase();
//        $this->connection->commit();
    }

    /**
     * Get database connection
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        return $this->container->getByType(Connection::class);
    }

    /**
     * Get database connection
     * @return Explorer
     */
    protected function getDbExplorer(): Explorer
    {
        return $this->container->getByType(Explorer::class);
    }

    /**
     * Empty database
     */
    protected function emptyDatabase(): void
    {
        $this->connection->query('DROP DATABASE IF EXISTS `oauth_test`');
        $this->connection->query('CREATE DATABASE `oauth_test`');
        $this->connection->query('USE `oauth_test`');
    }

    /**
     * Create database
     */
    protected function createDatabase(): void
    {
        $schema = array_filter(explode(';', $this->getSchemaSql()));
        $data = array_filter(explode(';', $this->getDataSql()));
        $testData = array_filter(explode(';', $this->getTestDataSql()));
        $queries = array_merge($schema, $data, $testData);

        foreach ($queries as $query) {
            if (!empty(trim($query))) {
                $this->connection->query($query);
            }
        }
    }

    /**
     * Get database schema SQL
     * @return string
     */
    protected function getSchemaSql(): string
    {
        return file_get_contents(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'sql', 'MySQL', 'migrations', 'V.0.0.1__create_base_oauth2_structure.sql']));
    }

    /**
     * Get database static data SQL
     * @return string
     */
    protected function getDataSql(): string
    {
        return file_get_contents(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'sql', 'MySQL', 'data', 'V.0.0.1__add_static_data.sql']));
    }

    /**
     * Get database test data SQL
     * @return string
     */
    protected function getTestDataSql(): string
    {
        preg_match('#(\w+)Test$#', static::class, $m);
        $file = $m[1] . '.data.sql';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    /**
     * Test tear down
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->emptyDatabase();
    }
}