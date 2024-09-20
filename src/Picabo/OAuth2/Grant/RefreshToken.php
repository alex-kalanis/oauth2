<?php

namespace Picabo\OAuth2\Grant;

use Picabo\OAuth2\Exceptions\InvalidScopeException;
use Picabo\OAuth2\Storage\AccessTokens\IAccessToken;
use Picabo\OAuth2\Storage\Clients\IClient;
use Picabo\OAuth2\Storage\Exceptions\InvalidRefreshTokenException;
use Picabo\OAuth2\Storage\ITokenFacade;
use Picabo\OAuth2\Storage\RefreshTokens\IRefreshToken;

/**
 * RefreshToken
 * @package Picabo\OAuth2\Grant
 * @author Drahomír Hanák
 */
class RefreshToken extends GrantType
{
    /**
     * Get refresh token identifier
     */
    public function getIdentifier(): string
    {
        return self::REFRESH_TOKEN;
    }

    /**
     * Verify request
     *
     * @throws InvalidRefreshTokenException
     */
    protected function verifyRequest(): void
    {
        $refreshTokenStorage = $this->token->getToken(ITokenFacade::REFRESH_TOKEN);
        $refreshToken = strval($this->input->getParameter('refresh_token'));

        $refreshTokenStorage->getEntity($refreshToken);
        $refreshTokenStorage->getStorage()->remove($refreshToken);
    }

    /**
     * Generate access token
     * @param IClient $client
     * @throws InvalidScopeException
     * @return array<string, string|int>
     */
    protected function generateAccessToken(IClient $client): array
    {
        $accessTokenStorage = $this->token->getToken(ITokenFacade::ACCESS_TOKEN);
        $refreshTokenStorage = $this->token->getToken(ITokenFacade::REFRESH_TOKEN);

        /** @var IAccessToken $accessToken */
        $accessToken = $accessTokenStorage->create($client, $this->user->getId());
        /** @var IRefreshToken $refreshToken */
        $refreshToken = $refreshTokenStorage->create($client, $this->user->getId());

        return [
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => 'bearer',
            'expires_in' => $accessTokenStorage->getLifetime(),
            'refresh_token' => $refreshToken->getRefreshToken(),
        ];
    }
}
