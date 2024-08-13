<?php

namespace Drahak\OAuth2\Storage\AccessTokens;

use Drahak\OAuth2\InvalidScopeException;

/**
 * Access token storage interface
 * @package Drahak\OAuth2\Storage
 * @author Drahomír Hanák
 */
interface IAccessTokenStorage
{

    /**
     * Store access token to given client access entity
     * @param IAccessToken $accessToken
     * @throws InvalidScopeException
     */
    public function store(IAccessToken $accessToken);

    /**
     * Remove access token from access entity
     * @param string $accessToken
     * @return void
     */
    public function remove($accessToken);

    /**
     * Get valid access token
     * @param string $accessToken
     * @return IAccessToken|NULL
     */
    public function getValidAccessToken($accessToken);

}