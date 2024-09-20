<?php

namespace Picabo\OAuth2\Grant;

use Picabo\OAuth2\Storage\AccessTokens\IAccessToken;
use Picabo\OAuth2\Storage\Clients\IClient;
use Picabo\OAuth2\Storage\ITokenFacade;
use Picabo\OAuth2\Exceptions\UnauthorizedClientException;
use Picabo\OAuth2\Storage\RefreshTokens\IRefreshToken;

/**
 * ClientCredentials
 * @package Picabo\OAuth2\Grant
 * @author Drahomír Hanák
 */
class ClientCredentials extends GrantType
{
    /**
     * Get identifier string to this grant type
     */
    public function getIdentifier(): string
    {
        return self::CLIENT_CREDENTIALS;
    }

    /**
     * Verify request
     * @throws UnauthorizedClientException
     */
    protected function verifyRequest(): void
    {
        if (!$this->input->getParameter(self::CLIENT_SECRET_KEY)) {
            throw new UnauthorizedClientException;
        }
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

        /** @var IAccessToken $accessToken */
        $accessToken = $accessTokenStorage->create($client, null, $this->getScope());
        /** @var IRefreshToken $refreshToken */
        $refreshToken = $refreshTokenStorage->create($client, null, $this->getScope());

        return [
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => 'bearer',
            'expires_in' => $accessTokenStorage->getLifetime(),
            'refresh_token' => $refreshToken->getRefreshToken(),
        ];
    }
}
