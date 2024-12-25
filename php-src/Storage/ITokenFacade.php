<?php

namespace kalanis\OAuth2\Storage;


use kalanis\OAuth2\Exceptions\InvalidScopeException;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\Exceptions\TokenException;


/**
 * ITokenFacade
 * @package kalanis\OAuth2\Storage
 */
interface ITokenFacade
{

    /** Default token names as defined in specification */
    public const ACCESS_TOKEN = 'access_token';
    public const REFRESH_TOKEN = 'refresh_token';
    public const AUTHORIZATION_CODE = 'authorization_code';

    /**
     * Create token
     * @param IClient $client
     * @param string|int|null $userId
     * @param array<string> $scope
     * @throws InvalidScopeException
     * @return ITokens
     */
    public function create(IClient $client, string|int|null $userId, array $scope = []): ITokens;

    /**
     * Returns token entity
     * @param string $token
     * @throws TokenException
     * @return ITokens
     */
    public function getEntity(string $token): ITokens;

    /**
     * Get token identifier name
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Get lifetime of token
     * @return int
     */
    public function getLifetime(): int;

    /**
     * Get actions over token storage
     * @return ITokenStorage
     */
    public function getStorage(): ITokenStorage;
}
