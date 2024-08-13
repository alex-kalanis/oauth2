<?php

namespace Tests\Drahak\OAuth2\Grant;

use Drahak\OAuth2\Http\IInput;
use Drahak\OAuth2\Storage\AccessTokens\AccessToken;
use Drahak\OAuth2\Storage\AccessTokens\IAccessToken;
use Drahak\OAuth2\Storage\AuthorizationCodes\AuthorizationCode;
use Drahak\OAuth2\Storage\Clients\IClient;
use Drahak\OAuth2\Storage\Clients\IClientStorage;
use Drahak\OAuth2\Storage\RefreshTokens\IRefreshToken;
use Drahak\OAuth2\Storage\RefreshTokens\RefreshToken;
use Drahak\OAuth2\Storage\TokenContext;
use Mockery;
use Nette\Security\User;
use Tests\TestCase;

abstract class GrantTestCase extends TestCase
{

    protected $client;
    protected $clientEntity;
    protected $accessTokenEntity;
    protected $refreshTokenEntity;
    protected $accessToken;
    protected $refreshToken;
    protected $authorizationCode;
    protected $user;
    protected $token;
    protected $input;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(IClientStorage::class);
        $this->clientEntity = Mockery::mock(IClient::class);
        $this->accessTokenEntity = Mockery::mock(IAccessToken::class);
        $this->refreshTokenEntity = Mockery::mock(IRefreshToken::class);
        $this->accessToken = Mockery::mock(AccessToken::class);
        $this->refreshToken = Mockery::mock(RefreshToken::class);
        $this->authorizationCode = Mockery::mock(AuthorizationCode::class);
        $this->token = Mockery::mock(TokenContext::class);
        $this->input = Mockery::mock(IInput::class);
        $this->user = Mockery::mock(User::class);
    }

    /**
     * Mock input data
     */
    protected function createInputMock(array $expectedData): void
    {
        foreach ($expectedData as $key => $value) {
            $this->input->expects('getParameter')
                ->once()
                ->with($key)
                ->andReturn($value);
        }
    }

    /**
     * Create tokens mocks
     * @param array $mocks identifier => MockInterface
     */
    protected function createTokenMocks(array $mocks): void
    {
        foreach ($mocks as $identifier => $mock) {
            $this->token->expects('getToken')
                ->atLeastOnce()
                ->with($identifier)
                ->andReturn($mock);
        }
    }
}
