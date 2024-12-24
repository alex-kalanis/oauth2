<?php

namespace kalanis\OAuth2\Grant;


use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\ITokenFacade;
use kalanis\OAuth2\Storage;


/**
 * AuthorizationCode
 * @package kalanis\OAuth2\Grant
 */
class AuthorizationCode extends GrantType
{

    /**
     * @var array<string>
     */
    private array $scope = [];

    /**
     * Get authorization code identifier
     */
    public function getIdentifier(): string
    {
        return self::AUTHORIZATION_CODE;
    }

    /**
     * Verify request
     * @throws Storage\Exceptions\InvalidAuthorizationCodeException
     */
    protected function verifyRequest(): void
    {
        $code = strval($this->input->getParameter('code'));

        $entity = $this->token->getToken(ITokenFacade::AUTHORIZATION_CODE)->getEntity($code);
        $this->scope = $entity->getScope();

        $this->token->getToken(ITokenFacade::AUTHORIZATION_CODE)->getStorage()->remove($code);
    }

    /**
     * @return array<string>
     */
    protected function getScope(): array
    {
        return $this->scope;
    }

    /**
     * Generate access token
     * @param IClient $client
     * @return array<string, string|int>
     */
    protected function generateAccessToken(IClient $client): array
    {
        $accessTokenStorage = $this->token->getToken(ITokenFacade::ACCESS_TOKEN);
        $refreshTokenStorage = $this->token->getToken(ITokenFacade::REFRESH_TOKEN);

        /** @var Storage\AccessTokens\IAccessToken $accessToken */
        $accessToken = $accessTokenStorage->create($client, $this->user->getId(), $this->getScope());
        /** @var Storage\RefreshTokens\IRefreshToken $refreshToken */
        $refreshToken = $refreshTokenStorage->create($client, $this->user->getId(), $this->getScope());

        return [
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => 'bearer',
            'expires_in' => $accessTokenStorage->getLifetime(),
            'refresh_token' => $refreshToken->getRefreshToken(),
        ];
    }
}
