<?php

namespace Picabo\OAuth2\Storage\AccessTokens;

use Picabo\OAuth2\Storage\ITokens;

/**
 * IAccessToken entity
 * @package Picabo\OAuth2\Storage\AccessTokens
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
