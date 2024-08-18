<?php

namespace Picabo\OAuth2\Grant;

use Picabo\OAuth2\Storage\Exceptions\InvalidRefreshTokenException;
use Picabo\OAuth2\Storage\ITokenFacade;

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
        $refreshToken = $this->input->getParameter('refresh_token');

        $refreshTokenStorage->getEntity($refreshToken);
        $refreshTokenStorage->getStorage()->remove($refreshToken);
    }

    /**
     * Generate access token
     * @return array<string, string|int>
     */
    protected function generateAccessToken(): array
    {
        $accessTokenStorage = $this->token->getToken(ITokenFacade::ACCESS_TOKEN);
        $refreshTokenStorage = $this->token->getToken(ITokenFacade::REFRESH_TOKEN);

        $accessToken = $accessTokenStorage->create($this->getClient(), $this->user->getId());
        $refreshToken = $refreshTokenStorage->create($this->getClient(), $this->user->getId());

        return [
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => 'bearer',
            'expires_in' => $accessTokenStorage->getLifetime(),
            'refresh_token' => $refreshToken->getRefreshToken(),
        ];
    }
}