<?php

namespace Drahak\OAuth2\Storage\RefreshTokens;

use Drahak\OAuth2\Storage\ITokens;

/**
 * IRefreshToken entity
 * @package Drahak\OAuth2\Storage\RefreshTokens
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
