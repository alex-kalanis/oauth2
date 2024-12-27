<?php

namespace kalanis\OAuth2\Storage\AccessTokens;


use DateTime;


/**
 * Base AccessToken entity
 * @package kalanis\OAuth2\Storage\AccessTokens
 */
class AccessToken implements IAccessToken
{

    /**
     * @param string $accessToken
     * @param DateTime $expires
     * @param string|int $clientId
     * @param string|int|null $userId
     * @param array<string> $scope
     */
    public function __construct(
        private readonly string $accessToken,
        private readonly DateTime $expires,
        private readonly string|int $clientId,
        private readonly string|int|null $userId,
        private readonly array $scope,
    )
    {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getClientId(): string|int
    {
        return $this->clientId;
    }

    public function getUserId(): string|int|null
    {
        return $this->userId;
    }

    public function getExpires(): DateTime
    {
        return $this->expires;
    }

    public function getScope(): array
    {
        return $this->scope;
    }
}
