<?php

namespace Drahak\OAuth2\Storage\AccessTokens;

use Drahak\OAuth2\Storage\ITokens;

/**
 * IAccessToken entity
 * @package Drahak\OAuth2\Storage\AccessTokens
 * @author Drahomír Hanák
 */
interface IAccessToken extends ITokens
{

    /**
     * Get access token
     * @return string
     */
    public function getAccessToken(): string;
}
