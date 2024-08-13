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
use Mockista\MockInterface;
use Nette\Security\User;
use Tests\TestCase;

/**
 * GrantTestCase
 * @package Tests\Drahak\OAuth2\Grant
 * @author Drahomír Hanák
 */
abstract class GrantTestCase extends TestCase
{

    protected MockInterface $client;
    protected MockInterface $clientEntity;
    protected MockInterface $accessTokenEntity;
    protected MockInterface $refreshTokenEntity;
    protected MockInterface $accessToken;
    protected MockInterface $refreshToken;
    protected MockInterface $authorizationCode;
    protected MockInterface $user;
    protected MockInterface $token;
    protected MockInterface $input;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->mockista->create(IClientStorage::class);
        $this->clientEntity = $this->mockista->create(IClient::class);
        $this->accessTokenEntity = $this->mockista->create(IAccessToken::class);
        $this->refreshTokenEntity = $this->mockista->create(IRefreshToken::class);
        $this->accessToken = $this->mockista->create(AccessToken::class);
        $this->refreshToken = $this->mockista->create(RefreshToken::class);
        $this->authorizationCode = $this->mockista->create(AuthorizationCode::class);
        $this->token = $this->mockista->create(TokenContext::class);
        $this->input = $this->mockista->create(IInput::class);
        $this->user = $this->mockista->create(User::class);
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
