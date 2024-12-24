<?php

namespace kalanis\OAuth2\Storage\AccessTokens;


use kalanis\OAuth2\Exceptions\InvalidScopeException;
use kalanis\OAuth2\Storage\ITokenStorage;


/**
 * Access token storage interface
 * @package kalanis\OAuth2\Storage
 */
interface IAccessTokenStorage extends ITokenStorage
{
    /**
     * Store access token to given client access entity
     * @throws InvalidScopeException
     * @return void
     */
    public function store(IAccessToken $accessToken): void;

    /**
     * Get valid access token
     * @param string $accessToken
     * @return IAccessToken|null
     */
    public function getValidAccessToken(string $accessToken): ?IAccessToken;
}
