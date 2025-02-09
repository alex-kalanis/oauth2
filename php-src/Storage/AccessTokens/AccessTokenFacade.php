<?php

namespace kalanis\OAuth2\Storage\AccessTokens;


use DateTime;
use kalanis\OAuth2\IKeyGenerator;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\Exceptions\InvalidAccessTokenException;
use kalanis\OAuth2\Storage\ITokenFacade;


/**
 * AccessToken
 * @package kalanis\OAuth2\Storage\AccessTokens
 */
class AccessTokenFacade implements ITokenFacade
{

    public function __construct(
        private readonly int $lifetime,
        private readonly IKeyGenerator $keyGenerator,
        private readonly IAccessTokenStorage $storage,
    )
    {
    }

    /**
     * Create access token
     */
    public function create(IClient $client, string|int|null $userId, array $scope = []): IAccessToken
    {
        $accessExpires = new DateTime;
        $accessExpires->modify('+' . $this->lifetime . ' seconds');

        $accessToken = new AccessToken(
            $this->keyGenerator->generate(),
            $accessExpires,
            $client->getId(),
            $userId,
            $scope
        );
        $this->storage->store($accessToken);

        return $accessToken;
    }

    /**
     * Check access token
     */
    public function getEntity(string $token): IAccessToken
    {
        $entity = $this->storage->getValidAccessToken($token);
        if (!$entity) {
            $this->storage->remove($token);
            throw new InvalidAccessTokenException;
        }
        return $entity;
    }

    /**
     * Get token identifier name
     */
    public function getIdentifier(): string
    {
        return self::ACCESS_TOKEN;
    }


    /**
     * Returns access token lifetime
     * @return int
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    /**
     * Get access token storage
     * @return IAccessTokenStorage
     */
    public function getStorage(): IAccessTokenStorage
    {
        return $this->storage;
    }
}
