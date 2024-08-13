<?php

namespace Drahak\OAuth2\Storage\AccessTokens;

use DateTime;
use Nette\SmartObject;

/**
 * Base AccessToken entity
 * @package Drahak\OAuth2\Storage\AccessTokens
 * @author Drahomír Hanák
 */
class AccessToken implements IAccessToken
{
    use SmartObject;

    public function __construct(
        private readonly string $accessToken,
        private readonly DateTime $expires,
        private readonly string|int $clientId,
        private readonly string|int $userId,
        private readonly array $scope
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

    public function getUserId(): string|int
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
