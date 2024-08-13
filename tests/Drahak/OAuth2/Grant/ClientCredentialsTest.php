<?php

namespace Tests\Drahak\OAuth2\Grant;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/GrantTestCase.php';

use Drahak\OAuth2\Grant\ClientCredentials;
use Drahak\OAuth2\Grant\GrantType;
use Drahak\OAuth2\Storage\ITokenFacade;
use Tester\Assert;

/**
 * Test: Tests\Drahak\OAuth2\Grant\ClientCredentials.
 *
 * @testCase Tests\Drahak\OAuth2\Grant\ClientCredentialsTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Grant
 */
class ClientCredentialsTest extends GrantTestCase
{

    public function testThrowsExceptionWhenClientSecretIsNotProvided(): void
    {
        $this->input->expects('getParameter')
            ->once()
            ->with(GrantType::CLIENT_SECRET_KEY)
            ->andReturn(NULL);

        Assert::throws(function () {
            $method = $this->grant->getReflection()->getMethod('verifyRequest');
            $method->setAccessible(TRUE);
            $method->invoke($this->grant);
        }, 'Drahak\OAuth2\UnauthorizedClientException');
    }

    public function testGenerateAccessToken(): void
    {
        $access = 'access token';
        $refresh = 'refresh token';
        $lifetime = 3600;

        $this->createInputMock(array(
            'client_id' => '64336132313361642d643134322d3131',
            'client_secret' => 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12',
            'scope' => NULL
        ));
        $this->createTokenMocks(array(
            ITokenFacade::ACCESS_TOKEN => $this->accessToken,
            ITokenFacade::REFRESH_TOKEN => $this->refreshToken
        ));

        $this->client->expects('getClient')->once()->andReturn($this->clientEntity);

        $this->accessToken->expects('create')->once()->with($this->clientEntity, NULL, array())->andReturn($this->accessTokenEntity);
        $this->accessToken->expects('getLifetime')->once()->andReturn($lifetime);
        $this->refreshToken->expects('create')->once()->with($this->clientEntity, NULL, array())->andReturn($this->refreshTokenEntity);

        $this->accessTokenEntity->expects('getAccessToken')->once()->andReturn($access);
        $this->refreshTokenEntity->expects('getRefreshToken')->once()->andReturn($refresh);

        $method = $this->grant->getReflection()->getMethod('generateAccessToken');
        $method->setAccessible(TRUE);
        $response = $method->invoke($this->grant);

        Assert::equal($response['access_token'], $access);
        Assert::equal($response['expires_in'], $lifetime);
        Assert::equal($response['refresh_token'], $refresh);
        Assert::equal($response['token_type'], 'bearer');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->grant = new ClientCredentials($this->input, $this->token, $this->client, $this->user);
    }

}

(new ClientCredentialsTest())->run();
