<?php

namespace Tests\Drahak\OAuth2\Grant;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/GrantTestCase.php';

use Drahak\OAuth2\Grant\Implicit;
use Drahak\OAuth2\Storage\ITokenFacade;
use Tester\Assert;

class ImplicitTest extends GrantTestCase
{

    /** @var Implicit */
    private $grant;

    public function testGenerateAccessToken(): void
    {
        $access = 'access token';
        $lifetime = 3600;

        $this->createInputMock(array(
            'client_id' => '64336132313361642d643134322d3131',
            'client_secret' => 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12',
            'scope' => NULL
        ));
        $this->createTokenMocks(array(
            ITokenFacade::ACCESS_TOKEN => $this->accessToken
        ));

        $this->client->expects('getClient')->once()->andReturn($this->clientEntity);

        $this->user->expects('getId')->once()->andReturn(1);
        $this->accessToken->expects('create')->once()->with($this->clientEntity, 1, array())->andReturn($this->accessTokenEntity);
        $this->accessToken->expects('getLifetime')->once()->andReturn($lifetime);

        $this->accessTokenEntity->expects('getAccessToken')->once()->andReturn($access);

        $method = $this->grant->getReflection()->getMethod('generateAccessToken');
        $method->setAccessible(TRUE);
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
