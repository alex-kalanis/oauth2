<?php

namespace kalanis\OAuth2\Storage\RefreshTokens;


use DateTime;
use kalanis\OAuth2\IKeyGenerator;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\Exceptions\InvalidRefreshTokenException;
use kalanis\OAuth2\Storage\ITokenFacade;


/**
 * RefreshToken
 * @package kalanis\OAuth2\Token
 */
class RefreshTokenFacade implements ITokenFacade
{
    public function __construct(
        private readonly int $lifetime,
        private readonly IKeyGenerator $keyGenerator,
        private readonly IRefreshTokenStorage $storage,
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
