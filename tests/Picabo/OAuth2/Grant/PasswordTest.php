<?php

namespace Tests\Picabo\OAuth2\Grant;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/GrantTestCase.php';

use Picabo\OAuth2\Grant\Password;
use Picabo\OAuth2\Storage\ITokenFacade;
use ReflectionClass;
use Tester\Assert;

class PasswordTest extends GrantTestCase
{

    private Password $grant;

    public function testVerifyRequest(): void
    {
        $data = ['username' => 'test', 'password' => 'some might say'];
        $this->createInputMock($data);

        $this->user->expects('login')
            ->once()
            ->with($data['username'], $data['password']);

        $reflection = new ReflectionClass($this->grant);
        $method = $reflection->getMethod('verifyRequest');
        $method->invoke($this->grant);
    }

    public function testGenerateAccessToken(): void
    {
        $access = 'access token';
        $refresh = 'refresh token';
        $lifetime = 3600;

        $this->input->expects('getParameter')->once()->with('client_id')->andReturn('64336132313361642d643134322d3131');
        $this->input->expects('getParameter')->once()->with('client_secret')->andReturn('a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12');
        $this->input->expects('getParameter')->once()->with('scope')->andReturn(null);

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
        $this->grant = new Password($this->input, $this->token, $this->client, $this->user);
    }

}

(new PasswordTest())->run();
