<?php

namespace Tests\Picabo\OAuth2\Grant;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/GrantTestCase.php';

use Picabo\OAuth2\Grant\Password;
use Picabo\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use Picabo\OAuth2\Storage\RefreshTokens\RefreshTokenFacade;
use ReflectionClass;
use Tester\Assert;

class PasswordTest extends GrantTestCase
{

    private Password $pass;

    public function testVerifyRequest(): void
    {
        $data = ['username' => 'test', 'password' => 'some might say'];
        $this->createInputMock($data);

        $this->user->expects('login')
            ->once()
            ->with($data['username'], $data['password']);

        $reflection = new ReflectionClass($this->pass);
        $method = $reflection->getMethod('verifyRequest');
        $method->invoke($this->pass);
    }

    public function testGenerateAccessToken(): void
    {
        $access = 'access token';
        $refresh = 'refresh token';
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
        $this->token->addToken(
            new RefreshTokenFacade(
                $lifetime,
                new XGenerator($refresh),
                new XRefreshStorage($this->refreshTokenEntity)
            )
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
            ->atLeast()
            ->once()
            ->andReturn(1);

        $this->accessTokenEntity
            ->expects('getAccessToken')
            ->once()
            ->andReturn($access);

        $this->refreshTokenEntity
            ->expects('getRefreshToken')
            ->once()
            ->andReturn($refresh);

        $reflection = new ReflectionClass($this->pass);
        $method = $reflection->getMethod('generateAccessToken');
        $response = $method->invoke($this->pass, $this->clientEntity);

        Assert::equal($response['access_token'], $access);
        Assert::equal($response['expires_in'], $lifetime);
        Assert::equal($response['refresh_token'], $refresh);
        Assert::equal($response['token_type'], 'bearer');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->pass = new Password($this->input, $this->token, $this->client, $this->user);
    }

}


(new PasswordTest())->run();
