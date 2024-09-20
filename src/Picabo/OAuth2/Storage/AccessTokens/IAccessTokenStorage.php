<?php

namespace Picabo\OAuth2\Storage\AccessTokens;

use Picabo\OAuth2\Exceptions\InvalidScopeException;
use Picabo\OAuth2\Storage\ITokenStorage;

/**
 * Access token storage interface
 * @package Picabo\OAuth2\Storage
 * @author Drahomír Hanák
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
