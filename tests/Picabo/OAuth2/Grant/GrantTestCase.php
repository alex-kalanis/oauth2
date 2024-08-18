<?php

namespace Tests\Picabo\OAuth2\Grant;

use Picabo\OAuth2\Http\IInput;
use Picabo\OAuth2\Storage\AccessTokens\AccessToken;
use Picabo\OAuth2\Storage\AccessTokens\IAccessToken;
use Picabo\OAuth2\Storage\AuthorizationCodes\AuthorizationCode;
use Picabo\OAuth2\Storage\Clients\IClient;
use Picabo\OAuth2\Storage\Clients\IClientStorage;
use Picabo\OAuth2\Storage\ITokenFacade;
use Picabo\OAuth2\Storage\RefreshTokens\IRefreshToken;
use Picabo\OAuth2\Storage\RefreshTokens\IRefreshTokenStorage;
use Picabo\OAuth2\Storage\RefreshTokens\RefreshToken;
use Picabo\OAuth2\Storage\TokenContext;
use Mockery;
use Nette\Security\User;
use Tests\TestCase;

abstract class GrantTestCase extends TestCase
{

    protected $client;
    protected $clientEntity;
    protected $accessTokenEntity;
    protected $refreshTokenEntity;
    protected $refreshTokenStorage;
    protected $accessToken;
    protected $refreshToken;
    protected $authorizationCode;
    protected $user;
    protected TokenContext $token;
    protected $input;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(IClientStorage::class);
        $this->clientEntity = Mockery::mock(IClient::class);
        $this->accessTokenEntity = Mockery::mock(IAccessToken::class);
        $this->refreshTokenEntity = Mockery::mock(IRefreshToken::class);
        $this->refreshTokenStorage = Mockery::mock(IRefreshTokenStorage::class);
        $this->accessToken = Mockery::mock(AccessToken::class);
        $this->refreshToken = Mockery::mock(RefreshToken::class);
        $this->authorizationCode = Mockery::mock(AuthorizationCode::class);
        $this->token = new TokenContext();
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
     * @param array<ITokenFacade> $mocks identifier => MockInterface
     */
    protected function createTokenMocks(array $mocks): void
    {
        foreach ($mocks as $mock) {
            $this->token->addToken($mock);
        }
    }
}
