<?php

namespace kalanis\OAuth2\Grant;


use kalanis\OAuth2\Exceptions\InvalidRequestException;
use kalanis\OAuth2\Exceptions\InvalidStateException;
use kalanis\OAuth2\Storage\AccessTokens\IAccessToken;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\ITokenFacade;
use kalanis\OAuth2\Storage\RefreshTokens\IRefreshToken;
use Nette\Security\AuthenticationException;


/**
 * Password grant type
 * @package kalanis\OAuth2\Grant
 */
class Password extends GrantType
{

    /**
     * Get identifier string to this grant type
     */
    public function getIdentifier(): string
    {
        return self::PASSWORD;
    }

    /**
     * Verify request
     *
     * @throws InvalidStateException
     * @throws InvalidRequestException
     */
    protected function verifyRequest(): void
    {
        $password = $this->input->getParameter('password');
        $username = $this->input->getParameter('username');
        if (!$password || !$username) {
            throw new InvalidStateException;
        }

        try {
            $this->user->login(strval($username), strval($password));
        } catch (AuthenticationException $e) {
            throw new InvalidRequestException('Wrong user credentials', $e);
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
        $accessToken = $accessTokenStorage->create($client, $this->user->getId(), $this->getScope());
        /** @var IRefreshToken $refreshToken */
        $refreshToken = $refreshTokenStorage->create($client, $this->user->getId(), $this->getScope());

        return [
            'access_token' => $accessToken->getAccessToken(),
            'expires_in' => $accessTokenStorage->getLifetime(),
            'token_type' => 'bearer',
            'refresh_token' => $refreshToken->getRefreshToken(),
        ];
    }
}
