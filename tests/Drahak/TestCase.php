<?php

namespace Tests;

use Mockista\Registry;
use Tester;

/**
 * TestCase
 * @package Tests
 * @author Drahomír Hanák
 */
abstract class TestCase extends Tester\TestCase
{

    /** @var Registry */
    protected $mockista;

    protected function setUp(): void
    {
        $this->mockista = new Registry;
    }
}
