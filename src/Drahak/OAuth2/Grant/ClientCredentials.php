<?php

namespace Drahak\OAuth2\Grant;

use Drahak\OAuth2\Storage\ITokenFacade;
use Drahak\OAuth2\UnauthorizedClientException;

/**
 * ClientCredentials
 * @package Drahak\OAuth2\Grant
 * @author Drahomír Hanák
 */
class ClientCredentials extends GrantType
{
    /**
     * Get identifier string to this grant type
     * @return string
     */
    public function getIdentifier()
    {
        return self::CLIENT_CREDENTIALS;
    }

    /**
     * Verify request
     * @throws UnauthorizedClientException
     */
    protected function verifyRequest()
    {
        if (!$this->input->getParameter(self::CLIENT_SECRET_KEY)) {
            throw new UnauthorizedClientException;
        }
    }

    /**
     * Generate access token
     * @return string
     */
    protected function generateAccessToken()
    {
        $client = $this->getClient();
        $accessTokenStorage = $this->token->getToken(ITokenFacade::ACCESS_TOKEN);
        $refreshTokenStorage = $this->token->getToken(ITokenFacade::REFRESH_TOKEN);

        $accessToken = $accessTokenStorage->create($client, NULL, $this->getScope());
        $refreshToken = $refreshTokenStorage->create($client, NULL, $this->getScope());

        return array(
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => 'bearer',
            'expires_in' => $accessTokenStorage->getLifetime(),
            'refresh_token' => $refreshToken->getRefreshToken()
        );
    }

}