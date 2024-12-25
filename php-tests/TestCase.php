<?php

namespace Tests;


use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use Tester;


/**
 * TestCase
 * @package Tests
 */
abstract class TestCase extends Tester\TestCase
{
    use MockeryPHPUnitIntegration;

    protected Container $container;

    public function __construct()
    {
        $configurator = new Configurator();

        $config = $configurator->setTempDirectory(TEMP_DIR);
        $config->addStaticParameters([
            'OAUTH2_MYSQL_DB_HOST' => strval(getenv('OAUTH2_MYSQL_DB_HOST')) ?: '127.0.0.1',
            'OAUTH2_MYSQL_DB_NAME' => strval(getenv('OAUTH2_MYSQL_DB_NAME')) ?: 'testing',
            'OAUTH2_MYSQL_DB_USER' => strval(getenv('OAUTH2_MYSQL_DB_USER')) ?: 'testing',
            'OAUTH2_MYSQL_DB_PASS' => strval(getenv('OAUTH2_MYSQL_DB_PASS')) ?: null,
        ]);
        $config->addConfig(__DIR__ . DIRECTORY_SEPARATOR . 'config.neon');

        $this->container = $config->createContainer();
    }
}
