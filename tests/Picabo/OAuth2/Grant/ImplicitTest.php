<?php

namespace Tests\Picabo\OAuth2\Grant;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/GrantTestCase.php';

use Picabo\OAuth2\Grant\Implicit;
use Picabo\OAuth2\Storage\ITokenFacade;
use Tester\Assert;

class ImplicitTest extends GrantTestCase
{

    private Implicit $grant;

    public function testGenerateAccessToken(): void
    {
        $access = 'access token';
        $lifetime = 3600;

        $this->createInputMock([
            'client_id' => '64336132313361642d643134322d3131',
            'client_secret' => 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12',
            'scope' => null
        ]);
        $this->createTokenMocks([
            ITokenFacade::ACCESS_TOKEN => $this->accessToken
        ]);

        $this->client->expects('getClient')->once()->andReturn($this->clientEntity);

        $this->user->expects('getId')->once()->andReturn(1);
        $this->accessToken->expects('create')->once()->with($this->clientEntity, 1, [])->andReturn($this->accessTokenEntity);
        $this->accessToken->expects('getLifetime')->once()->andReturn($lifetime);

        $this->accessTokenEntity->expects('getAccessToken')->once()->andReturn($access);

        $reflection = new \ReflectionClass($this->grant);
        $method = $reflection->getMethod('generateAccessToken');
        $response = $method->invoke($this->grant);

        Assert::equal($response['access_token'], $access);
        Assert::equal($response['expires_in'], $lifetime);
        Assert::equal($response['token_type'], 'bearer');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->grant = new Implicit($this->input, $this->token, $this->client, $this->user);
    }

}

(new ImplicitTest())->run();
