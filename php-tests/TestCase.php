<?php

namespace Tests;


use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tester;


/**
 * TestCase
 * @package Tests
 */
abstract class TestCase extends Tester\TestCase
{
    use MockeryPHPUnitIntegration;
}
