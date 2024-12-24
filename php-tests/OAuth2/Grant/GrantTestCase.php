<?php

namespace Tests\OAuth2\Grant;


use kalanis\OAuth2\Http\IInput;
use kalanis\OAuth2\IKeyGenerator;
use kalanis\OAuth2\Storage\AccessTokens\AccessToken;
use kalanis\OAuth2\Storage\AccessTokens\IAccessToken;
use kalanis\OAuth2\Storage\AccessTokens\IAccessTokenStorage;
use kalanis\OAuth2\Storage\AuthorizationCodes\AuthorizationCode;
use kalanis\OAuth2\Storage\AuthorizationCodes\IAuthorizationCode;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\Clients\IClientStorage;
use kalanis\OAuth2\Storage\ITokenFacade;
use kalanis\OAuth2\Storage\RefreshTokens\IRefreshToken;
use kalanis\OAuth2\Storage\RefreshTokens\IRefreshTokenStorage;
use kalanis\OAuth2\Storage\RefreshTokens\RefreshToken;
use kalanis\OAuth2\Storage\TokenContext;
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
    protected TokenContext $token;
    protected $tokenFacade;
    protected $input;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(IClientStorage::class);
        $this->clientEntity = Mockery::mock(IClient::class);
        $this->accessTokenEntity = Mockery::mock(IAccessToken::class);
        $this->refreshTokenEntity = Mockery::mock(IRefreshToken::class);
        $this->refreshTokenStorage = Mockery::mock(IRefreshTokenStorage::class);
        $this->accessToken = Mockery::mock(AccessToken::class, IAccessToken::class);
        $this->refreshToken = Mockery::mock(RefreshToken::class, IRefreshToken::class);
        $this->authorizationCode = Mockery::mock(AuthorizationCode::class, IAuthorizationCode::class);
        $this->token = new TokenContext();
        $this->tokenFacade = Mockery::mock(ITokenFacade::class);
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
}


class XGenerator implements IKeyGenerator
{
    public function __construct(
        private readonly string $dummyKey
    )
    {
    }

    public function generate(int $length = 40): string
    {
        return $this->dummyKey;
    }
}


class XAccessStorage implements IAccessTokenStorage
{
    public function __construct(
        private readonly IAccessToken $accessToken,
    )
    {
    }

    public function store(IAccessToken $accessToken): void
    {
    }

    public function remove(string $token): void
    {
    }

    public function getValidAccessToken(string $accessToken): ?IAccessToken
    {
        return empty($accessToken) ? null : $this->accessToken;
    }
}


class XRefreshStorage implements IRefreshTokenStorage
{
    public function __construct(
        private readonly IRefreshToken $token,
    )
    {
    }

    public function store(IRefreshToken $refreshToken): void
    {
    }

    public function remove(string $token): void
    {
    }

    public function getValidRefreshToken(string $refreshToken): ?IRefreshToken
    {
        return (empty($refreshToken)) ? null : $this->token;
    }
}
