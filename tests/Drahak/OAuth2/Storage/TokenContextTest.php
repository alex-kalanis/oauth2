<?php

namespace Tests\Drahak\OAuth2\Storage;

require_once __DIR__ . '/../../bootstrap.php';

use Drahak\OAuth2\Storage\TokenContext;
use Mockista\MockInterface;
use Tester\Assert;
use Tests\TestCase;


/**
 * Test: Tests\Drahak\OAuth2\Storage\TokenContext.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\TokenContextTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage
 */
class TokenContextTest extends TestCase
{

    /** @var MockInterface */
    private $token;

    /** @var TokenContext */
    private $context;

    public function testGetInvalidToken(): void
    {
        Assert::throws(function () {
            $this->context->getToken('totally doesn\'t exist');
        }, 'Drahak\OAuth2\InvalidStateException');
    }

    public function testAddToken(): void
    {
        $this->token->expects('getIdentifier')->once()->andReturn('secured_token');
        $this->context->addToken($this->token);

        Assert::same($this->context->getToken('secured_token'), $this->token);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->mockista->create('Drahak\OAuth2\Storage\ITokenFacade');
        $this->context = new TokenContext;
    }

}

(new TokenContextTest())->run();
