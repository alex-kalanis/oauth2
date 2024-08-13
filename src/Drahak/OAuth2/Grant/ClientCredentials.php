<?php

namespace Drahak\OAuth2\Grant;

use Drahak\OAuth2\Storage\ITokenFacade;
use Drahak\OAuth2\Exceptions\UnauthorizedClientException;

/**
 * ClientCredentials
 * @package Drahak\OAuth2\Grant
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
     * @return array<string, string|int>
     */
    protected function generateAccessToken(): array
    {
        $client = $this->getClient();
        $accessTokenStorage = $this->token->getToken(ITokenFacade::ACCESS_TOKEN);
        $refreshTokenStorage = $this->token->getToken(ITokenFacade::REFRESH_TOKEN);

        $accessToken = $accessTokenStorage->create($client, NULL, $this->getScope());
        $refreshToken = $refreshTokenStorage->create($client, NULL, $this->getScope());

        return [
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => 'bearer',
            'expires_in' => $accessTokenStorage->getLifetime(),
            'refresh_token' => $refreshToken->getRefreshToken(),
        ];
    }
}
