<?php

namespace Drahak\OAuth2\Storage\RefreshTokens;

use DateTime;
use Drahak\OAuth2\IKeyGenerator;
use Drahak\OAuth2\Storage\Clients\IClient;
use Drahak\OAuth2\Storage\Exceptions\InvalidRefreshTokenException;
use Drahak\OAuth2\Storage\ITokenFacade;
use Drahak\OAuth2\Storage\ITokens;
use Nette\SmartObject;

/**
 * RefreshToken
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
class RefreshTokenFacade implements ITokenFacade
{
    use SmartObject;

    public function __construct(
        private readonly int $lifetime,
        private readonly IKeyGenerator $keyGenerator,
        private readonly IRefreshTokenStorage $storage
    )
    {
    }

    /**
     * Create new refresh token
     */
    public function create(IClient $client, string|int $userId, array $scope = []): ?ITokens
    {
        $expires = new DateTime;
        $expires->modify('+' . $this->lifetime . ' seconds');
        $refreshToken = new RefreshToken(
            $this->keyGenerator->generate(),
            $expires,
            $client->getId(),
            $userId
        );
        $this->storage->store($refreshToken);

        return $refreshToken;
    }

    /**
     * Get refresh token entity
     */
    public function getEntity(string $token): ?ITokens
    {
        $entity = $this->storage->getValidRefreshToken($token);
        if (!$entity) {
            $this->storage->remove($token);
            throw new InvalidRefreshTokenException;
        }
        return $entity;
    }

    /**
     * Get token identifier name
     */
    public function getIdentifier(): string
    {
        return self::REFRESH_TOKEN;
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
     * @return IRefreshTokenStorage
     */
    public function getStorage(): IRefreshTokenStorage
    {
        return $this->storage;
    }
}
