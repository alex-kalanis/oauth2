<?php

namespace Tests\OAuth2\Grant;


require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GrantTestCase.php';


use kalanis\OAuth2\Grant\ClientCredentials;
use kalanis\OAuth2\Grant\GrantType;
use kalanis\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use kalanis\OAuth2\Storage\RefreshTokens\RefreshTokenFacade;
use ReflectionClass;
use Tester\Assert;


class ClientCredentialsTest extends GrantTestCase
{
    protected ClientCredentials $credentials;

    public function testThrowsExceptionWhenClientSecretIsNotProvided(): void
    {
        $this->input->expects('getParameter')
            ->once()
            ->with(GrantType::CLIENT_SECRET_KEY)
            ->andReturn(null);

        Assert::throws(function () {
            $reflection = new ReflectionClass($this->credentials);
            $method = $reflection->getMethod('verifyRequest');
            $method->invoke($this->credentials);
        }, \kalanis\OAuth2\Exceptions\UnauthorizedClientException::class);
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

        $reflection = new ReflectionClass($this->credentials);
        $method = $reflection->getMethod('generateAccessToken');
        $response = $method->invoke($this->credentials, $this->clientEntity);

        Assert::equal($response['access_token'], $access);
        Assert::equal($response['expires_in'], $lifetime);
        Assert::equal($response['refresh_token'], $refresh);
        Assert::equal($response['token_type'], 'bearer');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->credentials = new ClientCredentials($this->input, $this->token, $this->client, $this->user);
    }

}


(new ClientCredentialsTest())->run();
