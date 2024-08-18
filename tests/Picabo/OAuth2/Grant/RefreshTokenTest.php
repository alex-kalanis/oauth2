<?php

namespace Tests\Picabo\OAuth2\Grant;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/GrantTestCase.php';

use Picabo\OAuth2\Grant\RefreshToken;
use Picabo\OAuth2\KeyGenerator;
use Picabo\OAuth2\Storage\ITokenFacade;
use Mockery;
use Picabo\OAuth2\Storage\RefreshTokens\RefreshTokenFacade;
use ReflectionClass;
use Tester\Assert;

class RefreshTokenTest extends GrantTestCase
{

    private RefreshToken $grant;

    public function testVerifyRequest(): void
    {
        $this->refreshTokenStorage->expects('store');
        $this->refreshTokenStorage->expects('remove');
        $this->refreshTokenStorage->expects('getValidRefreshToken')->andReturn(null);

        $data = ['refresh_token' => '98b2950c11d8f3aa5773993ce0db712809524eeb4e625db00f39fb1530eee4ec'];
        $this->createInputMock($data);
        $this->createTokenMocks([new RefreshTokenFacade(
            999,
            new KeyGenerator(),
            $this->refreshTokenStorage
        )]);

        $storage = Mockery::mock(\Picabo\OAuth2\Storage\RefreshTokens\IRefreshTokenStorage::class);
        $storage->expects('remove')->once()->with($data['refresh_token']);
        $this->refreshToken->expects('getEntity')->once()->with($data['refresh_token']);
        $this->refreshToken->expects('getStorage')->once()->andReturn($storage);

        Assert::throws(function () {
            $reflection = new ReflectionClass($this->grant);
            $method = $reflection->getMethod('verifyRequest');
            $method->invoke($this->grant);
        }, \Picabo\OAuth2\Storage\Exceptions\InvalidRefreshTokenException::class);
    }

    public function testGenerateAccessToken(): void
    {
        $this->refreshTokenStorage->expects('store');
        $this->refreshTokenStorage->expects('remove');
        $this->refreshTokenStorage->expects('getValidRefreshToken')->andReturn(null);

        $access = 'access token';
        $refresh = 'refresh token';
        $lifetime = 3600;

        $this->createInputMock([
            'client_id' => '64336132313361642d643134322d3131',
            'client_secret' => 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12'
        ]);
        $this->createTokenMocks([
            ITokenFacade::ACCESS_TOKEN => $this->accessToken,
            ITokenFacade::REFRESH_TOKEN => $this->refreshToken
        ]);

        $this->client->expects('getClient')->once()->andReturn($this->clientEntity);

        $this->user->expects('getId')->atLeast()->once()->andReturn(1);
        $this->accessToken->expects('create')->once()->with($this->clientEntity, 1)->andReturn($this->accessTokenEntity);
        $this->accessToken->expects('getLifetime')->once()->andReturn($lifetime);
        $this->refreshToken->expects('create')->once()->with($this->clientEntity, 1)->andReturn($this->refreshTokenEntity);

        $this->accessTokenEntity->expects('getAccessToken')->once()->andReturn($access);
        $this->refreshTokenEntity->expects('getRefreshToken')->once()->andReturn($refresh);

        $reflection = new ReflectionClass($this->grant);
        $method = $reflection->getMethod('generateAccessToken');
        $response = $method->invoke($this->grant);

        Assert::equal($response['access_token'], $access);
        Assert::equal($response['expires_in'], $lifetime);
        Assert::equal($response['refresh_token'], $refresh);
        Assert::equal($response['token_type'], 'bearer');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->grant = new RefreshToken($this->input, $this->token, $this->client, $this->user);
    }

}

(new RefreshTokenTest())->run();
