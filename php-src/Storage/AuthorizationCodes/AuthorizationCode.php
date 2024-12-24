<?php

namespace kalanis\OAuth2\Storage\AuthorizationCodes;


use DateTime;
use Nette\SmartObject;


/**
 * Base AuthorizationCode entity
 * @package kalanis\OAuth2\Storage\AuthorizationCodes
 */
class AuthorizationCode implements IAuthorizationCode
{
    use SmartObject;

    /**
     * @param string $authorizationCode
     * @param DateTime $expires
     * @param string|int $clientId
     * @param string|int|null $userId
     * @param array<string> $scope
     */
    public function __construct(
        private readonly string $authorizationCode,
        private readonly DateTime $expires,
        private readonly string|int $clientId,
        private readonly string|int|null $userId,
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
