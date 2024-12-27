<?php

namespace kalanis\OAuth2\Storage\RefreshTokens;


use DateTime;


/**
 * RefreshToken
 * @package kalanis\OAuth2\Storage\RefreshTokens
 */
class RefreshToken implements IRefreshToken
{
    /**
     * @param string $refreshToken
     * @param DateTime $expires
     * @param string|int $clientId
     * @param string|int|null $userId
     * @param array<string> $scope
     */
    public function __construct(
        private readonly string $refreshToken,
        private readonly DateTime $expires,
        private readonly string|int $clientId,
        private readonly string|int|null $userId,
        private readonly array $scope = [],
    )
    {
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getExpires(): DateTime
    {
        return $this->expires;
    }

    public function getClientId(): string|int
    {
        return $this->clientId;
    }

    public function getUserId(): string|int|null
    {
        return $this->userId;
    }

    public function getScope(): array
    {
        return $this->scope;
    }
}
