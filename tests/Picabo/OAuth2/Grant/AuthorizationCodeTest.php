<?php

namespace Tests\Picabo\OAuth2\Grant;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/GrantTestCase.php';

use Picabo\OAuth2\Grant\AuthorizationCode;
use Picabo\OAuth2\Storage\ITokenFacade;
use Picabo\OAuth2\Storage\AuthorizationCodes;
use Mockery;
use ReflectionClass;
use Tester\Assert;

class AuthorizationCodeTest extends GrantTestCase
{

    private AuthorizationCode $grant;

    public function testVerifyRequest(): void
    {
        $data = ['code' => '98b2950c11d8f3aa5773993ce0db712809524eeb4e625db00f39fb1530eee4ec'];

        $entity = Mockery::mock(AuthorizationCodes\IAuthorizationCode::class);
        $storage = Mockery::mock(AuthorizationCodes\IAuthorizationCode::class);

        $storage->expects('remove')
            ->once()
            ->with($data['code']);

        $this->createInputMock($data);
        $this->token->expects('getToken')
            ->atLeast()
            ->once()
            ->with(ITokenFacade::AUTHORIZATION_CODE)
            ->andReturn($this->authorizationCode);

        $this->authorizationCode->expects('getEntity')
            ->once()
            ->with($data['code'])
            ->andReturn($entity);

        $this->authorizationCode->expects('getStorage')
            ->once()
            ->andReturn($storage);

        $entity->expects('getScope')
            ->once()
            ->andReturn([]);

        Assert::throws(function () {
            $reflection = new ReflectionClass($this->grant);
            $method = $reflection->getMethod('verifyRequest');
            $method->invoke($this->grant);
        }, \Picabo\OAuth2\Exceptions\UnauthorizedClientException::class);
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
        $this->createTokenMocks([
            ITokenFacade::ACCESS_TOKEN => $this->accessToken,
            ITokenFacade::REFRESH_TOKEN => $this->refreshToken
        ]);

        $this->client->expects('getClient')->once()->andReturn($this->clientEntity);

        $this->user->expects('getId')->atLeast()->once()->andReturn(1);
        $this->accessToken->expects('create')->once()->with($this->clientEntity, 1, [])->andReturn($this->accessTokenEntity);
        $this->accessToken->expects('getLifetime')->once()->andReturn($lifetime);
        $this->refreshToken->expects('create')->once()->with($this->clientEntity, 1, [])->andReturn($this->refreshTokenEntity);

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
        $this->grant = new AuthorizationCode($this->input, $this->token, $this->client, $this->user);
    }

}

(new AuthorizationCodeTest())->run();
