<?php

namespace Drahak\OAuth2\Storage\AuthorizationCodes;

use DateTime;
use Drahak\OAuth2\IKeyGenerator;
use Drahak\OAuth2\Storage\Clients\IClient;
use Drahak\OAuth2\Storage\Exceptions\InvalidAuthorizationCodeException;
use Drahak\OAuth2\Storage\ITokenFacade;
use Drahak\OAuth2\Storage\ITokens;
use Nette\SmartObject;

/**
 * AuthorizationCode
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
class AuthorizationCodeFacade implements ITokenFacade
{
    use SmartObject;

    public function __construct(
        private readonly int $lifetime,
        private readonly IKeyGenerator $keyGenerator,
        private readonly IAuthorizationCodeStorage $storage
    )
    {
    }

    /**
     * Create authorization code
     */
    public function create(IClient $client, string|int $userId, array $scope = []): ITokens
    {
        $accessExpires = new DateTime;
        $accessExpires->modify('+' . $this->lifetime . ' seconds');

        $authorizationCode = new AuthorizationCode(
            $this->keyGenerator->generate(),
            $accessExpires,
            $client->getId(),
            $userId,
            $scope
        );
        $this->storage->store($authorizationCode);

        return $authorizationCode;
    }

    /**
     * Get authorization code entity
     */
    public function getEntity(string $token): ?ITokens
    {
        $entity = $this->storage->getValidAuthorizationCode($token);
        if (!$entity) {
            $this->storage->remove($token);
            throw new InvalidAuthorizationCodeException;
        }
        return $entity;
    }

    /**
     * Get token identifier name
     */
    public function getIdentifier(): string
    {
        return self::AUTHORIZATION_CODE;
    }


    /****************** Getters & setters ******************/

    /**
     * Get token lifetime
     * @return int
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    /**
     * Get storage
     */
    public function getStorage(): IAuthorizationCodeStorage
    {
        return $this->storage;
    }
}
