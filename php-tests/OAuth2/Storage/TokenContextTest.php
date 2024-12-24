<?php

namespace Tests\OAuth2\Storage;


require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\OAuth2\Storage\TokenContext;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class TokenContextTest extends TestCase
{

    private $token;

    /** @var TokenContext */
    private $context;

    public function testGetInvalidToken(): void
    {
        Assert::throws(function () {
            $this->context->getToken('totally doesn\'t exist');
        }, \kalanis\OAuth2\Exceptions\InvalidStateException::class);
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
        $this->token = Mockery::mock(\kalanis\OAuth2\Storage\ITokenFacade::class);
        $this->context = new TokenContext;
    }
}


(new TokenContextTest())->run();
