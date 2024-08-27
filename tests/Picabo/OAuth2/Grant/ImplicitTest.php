<?php

namespace Tests\Picabo\OAuth2\Grant;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/GrantTestCase.php';

use Picabo\OAuth2\Grant\Implicit;
use Picabo\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use Tester\Assert;

class ImplicitTest extends GrantTestCase
{

    private Implicit $implicit;

    public function testGenerateAccessToken(): void
    {
        $access = 'access token';
        $lifetime = 3600;

        $this->createInputMock([
            'client_id' => '64336132313361642d643134322d3131',
            'client_secret' => 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12',
            'scope' => null
        ]);

        $this->token->addToken(
            new AccessTokenFacade(
                $lifetime,
                new XGenerator($access),
                new XAccessStorage($this->accessTokenEntity)
            ),
        );

        $this->clientEntity
            ->expects('getId')
            ->once()
            ->andReturn(1);

        $this->client
            ->expects('getClient')
            ->once()
            ->andReturn($this->clientEntity);

        $this->user
            ->expects('getId')
            ->once()
            ->andReturn(1);

        $this->accessTokenEntity
            ->expects('getAccessToken')
            ->once()
            ->andReturn($access);

        $reflection = new \ReflectionClass($this->implicit);
        $method = $reflection->getMethod('generateAccessToken');
        $response = $method->invoke($this->implicit);

        Assert::equal($response['access_token'], $access);
        Assert::equal($response['expires_in'], $lifetime);
        Assert::equal($response['token_type'], 'bearer');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->implicit = new Implicit($this->input, $this->token, $this->client, $this->user);
    }
}


(new ImplicitTest())->run();
