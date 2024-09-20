<?php

namespace Tests\Picabo\OAuth2\Grant;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/GrantTestCase.php';

use Picabo\OAuth2\Grant\AuthorizationCode;
use Picabo\OAuth2\IKeyGenerator;
use Picabo\OAuth2\KeyGenerator;
use Picabo\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use Picabo\OAuth2\Storage\AuthorizationCodes;
use Mockery;
use Picabo\OAuth2\Storage\RefreshTokens\RefreshTokenFacade;
use ReflectionClass;
use Tester\Assert;

class AuthorizationCodeTest extends GrantTestCase
{

    private AuthorizationCode $authorization;

    public function testVerifyRequest(): void
    {
        $data = ['code' => '98b2950c11d8f3aa5773993ce0db712809524eeb4e625db00f39fb1530eee4ec'];

        $entity = Mockery::mock(AuthorizationCodes\IAuthorizationCode::class);
        $authStorage = Mockery::mock(AuthorizationCodes\IAuthorizationCodeStorage::class);

        $authStorage->expects('getValidAuthorizationCode')
            ->once()
            ->andReturn(null);

        $authStorage->expects('remove')
            ->once();

        // the exception is thrown here
        $this->token->addToken(
            new AuthorizationCodes\AuthorizationCodeFacade(
                999,
                Mockery::mock(KeyGenerator::class, IKeyGenerator::class),
                $authStorage
            )
        );

        $this->createInputMock($data);

        $entity->expects('getScope')
            ->once()
            ->andReturn([]);

        Assert::throws(function () {
            $reflection = new ReflectionClass($this->authorization);
            $method = $reflection->getMethod('verifyRequest');
            $method->invoke($this->authorization);
        }, \Picabo\OAuth2\Storage\Exceptions\InvalidAuthorizationCodeException::class);
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

        $reflection = new ReflectionClass($this->authorization);
        $method = $reflection->getMethod('generateAccessToken');
        $response = $method->invoke($this->authorization, $this->clientEntity);

        Assert::equal($response['access_token'], $access);
        Assert::equal($response['expires_in'], $lifetime);
        Assert::equal($response['refresh_token'], $refresh);
        Assert::equal($response['token_type'], 'bearer');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->authorization = new AuthorizationCode($this->input, $this->token, $this->client, $this->user);
    }
}


(new AuthorizationCodeTest())->run();
