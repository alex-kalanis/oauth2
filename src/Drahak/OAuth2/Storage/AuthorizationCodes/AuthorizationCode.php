<?php

namespace Drahak\OAuth2\Storage\AuthorizationCodes;

use DateTime;
use Nette\SmartObject;

/**
 * Base AuthorizationCode entity
 * @package Drahak\OAuth2\Storage\AuthorizationCodes
 * @author Drahomír Hanák
 */
class AuthorizationCode implements IAuthorizationCode
{
    use SmartObject;

    public function __construct(
        private readonly string $authorizationCode,
        private readonly DateTime $expires,
        private readonly string|int $clientId,
        private readonly string|int $userId,
        private readonly array $scope
    )
    {
    }

    public function getAuthorizationCode(): string
    {
        return $this->authorizationCode;
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
