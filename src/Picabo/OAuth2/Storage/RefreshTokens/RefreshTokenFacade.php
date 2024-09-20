<?php

namespace Picabo\OAuth2\Storage\RefreshTokens;

use DateTime;
use Picabo\OAuth2\IKeyGenerator;
use Picabo\OAuth2\Storage\Clients\IClient;
use Picabo\OAuth2\Storage\Exceptions\InvalidRefreshTokenException;
use Picabo\OAuth2\Storage\ITokenFacade;
use Picabo\OAuth2\Storage\ITokens;
use Nette\SmartObject;

/**
 * RefreshToken
 * @package Picabo\OAuth2\Token
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
    public function create(IClient $client, string|int|null $userId, array $scope = []): IRefreshToken
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
    public function getEntity(string $token): IRefreshToken
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
