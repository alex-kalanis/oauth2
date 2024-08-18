<?php

namespace Picabo\OAuth2\Storage\RefreshTokens;

use Picabo\OAuth2\Storage\ITokens;

/**
 * IRefreshToken entity
 * @package Picabo\OAuth2\Storage\RefreshTokens
 * @author Drahomír Hanák
 */
interface IRefreshToken extends ITokens
{

    /**
     * Get refresh token
     * @return string
     */
    public function getRefreshToken(): string;
}
