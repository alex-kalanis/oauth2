<?php

namespace Tests\OAuth2\Grant;


require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GrantTestCase.php';


use kalanis\OAuth2\Grant\RefreshToken;
use kalanis\OAuth2\IKeyGenerator;
use kalanis\OAuth2\KeyGenerator;
use kalanis\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use kalanis\OAuth2\Storage\RefreshTokens\RefreshTokenFacade;
use Mockery;
use kalanis\OAuth2\Storage\RefreshTokens;
use ReflectionClass;
use Tester\Assert;


class RefreshTokenTest extends GrantTestCase
{

    private RefreshToken $refresh;

    public function testVerifyRequest(): void
    {
        $data = ['refresh_token' => '98b2950c11d8f3aa5773993ce0db712809524eeb4e625db00f39fb1530eee4ec'];
        $this->createInputMock($data);

        $entity = Mockery::mock(RefreshTokens\IRefreshToken::class);
        $authStorage = Mockery::mock(RefreshTokens\IRefreshTokenStorage::class);

        $authStorage->expects('getValidRefreshToken')
            ->once()
            ->andReturn(null);

        $authStorage->expects('remove')
            ->once();

        // the exception is thrown here
        $this->token->addToken(
            new RefreshTokens\RefreshTokenFacade(
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
            $reflection = new ReflectionClass($this->refresh);
            $method = $reflection->getMethod('verifyRequest');
            $method->invoke($this->refresh);
        }, \kalanis\OAuth2\Storage\Exceptions\InvalidRefreshTokenException::class);
    }

    public function testGenerateAccessToken(): void
    {
        $access = 'access token';
        $refresh = 'refresh token';
        $lifetime = 3600;

        $this->createInputMock([
            'client_id' => '64336132313361642d643134322d3131',
            'client_secret' => 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12'
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

        $reflection = new ReflectionClass($this->refresh);
        $method = $reflection->getMethod('generateAccessToken');
        $response = $method->invoke($this->refresh, $this->clientEntity);

        Assert::equal($response['access_token'], $access);
        Assert::equal($response['expires_in'], $lifetime);
        Assert::equal($response['refresh_token'], $refresh);
        Assert::equal($response['token_type'], 'bearer');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->refresh = new RefreshToken($this->input, $this->token, $this->client, $this->user);
    }

}


(new RefreshTokenTest())->run();
