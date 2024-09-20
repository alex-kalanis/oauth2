<?php

namespace Picabo\OAuth2\Grant;

use Picabo\OAuth2\Storage\AccessTokens\IAccessToken;
use Picabo\OAuth2\Storage\Clients\IClient;
use Picabo\OAuth2\Storage\ITokenFacade;

/**
 * Implicit grant type
 * @package Picabo\OAuth2\Grant
 * @author Drahomír Hanák
 */
class Implicit extends GrantType
{

    /**
     * Get identifier string to this grant type
     */
    public function getIdentifier(): string
    {
        return self::IMPLICIT;
    }

    /**
     * Verify grant type
     */
    protected function verifyGrantType(IClient $client): void
    {
    }

    /**
     * Verify request
     * @return void
     */
    protected function verifyRequest(): void
    {
    }

    /**
     * Generate access token
     * @param IClient $client
     * @return array<string, string|int>
     */
    protected function generateAccessToken(IClient $client): array
    {
        $accessTokenStorage = $this->token->getToken(ITokenFacade::ACCESS_TOKEN);
        /** @var IAccessToken $accessToken */
        $accessToken = $accessTokenStorage->create($client, $this->user->getId(), $this->getScope());

        return [
            'access_token' => $accessToken->getAccessToken(),
            'expires_in' => $accessTokenStorage->getLifetime(),
            'token_type' => 'bearer'
        ];
    }
}
