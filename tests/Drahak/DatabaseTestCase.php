<?php

namespace Tests;

use Nette\Bootstrap\Configurator;
use Nette\Database\Connection;
use Nette\Database\SelectionFactory;
use Nette\DI\Container;
use Nette\Templating\Helpers;

/**
 * DatabaseTestCase
 * @package Tests
 * @author Drahomír Hanák
 */
abstract class DatabaseTestCase extends TestCase
{

    /** @var Connection */
    protected $connection;

    /** @var SelectionFactory */
    protected $selectionFactory;

    /** @var Container */
    private $container;

    public function __construct()
    {
        $configurator = new Configurator();

        $this->container = $configurator->setTempDirectory(TEMP_DIR)
            ->addConfig(__DIR__ . '/config.neon')
            ->createContainer();
    }

    /**
     * Setup database
     */
    protected function setUp(): void
    {
        parent::setUp();
//        \Tester\Helpers::lock('db', dirname(TEMP_DIR));

        $this->connection = $this->getConnection();
        $this->selectionFactory = $this->getSelectionFactory();

        $this->connection->beginTransaction();
        $this->emptyDatabase();
        $this->createDatabase();
        $this->connection->commit();
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
     * @return Connection
     */
    protected function getSelectionFactory()
    {
        return $this->container->getByType('Nette\Database\SelectionFactory');
    }

    /**
     * Empty database
     */
    protected function emptyDatabase(): void
    {
        $this->connection->query('DROP DATABASE oauth_test');
        $this->connection->query('CREATE DATABASE oauth_test');
        $this->connection->query('USE oauth_test');
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
            $this->connection->query($query);
        }
    }

    /**
     * Get database schema SQL
     * @return string
     */
    protected function getSchemaSql(): string
    {
        return file_get_contents(__DIR__ . '/../../sql/schema.sql');
    }

    /**
     * Get database static data SQL
     * @return string
     */
    protected function getDataSql(): string
    {
        return file_get_contents(__DIR__ . '/../../sql/data.sql');
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
