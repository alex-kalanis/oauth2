<?php

namespace Picabo\OAuth2\Storage\RefreshTokens;

use Picabo\OAuth2\Storage\ITokenStorage;

/**
 * IRefreshTokenStorage
 * @package Picabo\OAuth2\Storage\RefreshTokens
 * @author Drahomír Hanák
 */
interface IRefreshTokenStorage extends ITokenStorage
{

    /**
     * Store refresh token entity
     * @param IRefreshToken $refreshToken
     * @return void
     */
    public function store(IRefreshToken $refreshToken): void;

    /**
     * Validate refresh token
     * @param string $refreshToken
     * @return IRefreshToken|null
     */
    public function getValidRefreshToken(string $refreshToken): ?IRefreshToken;
}
