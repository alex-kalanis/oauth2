<?php

namespace Drahak\OAuth2\Storage\RefreshTokens;

/**
 * IRefreshTokenStorage
 * @package Drahak\OAuth2\Storage\RefreshTokens
 * @author Drahomír Hanák
 */
interface IRefreshTokenStorage
{

    /**
     * Store refresh token entity
     * @param IRefreshToken $refreshToken
     * @return void
     */
    public function store(IRefreshToken $refreshToken): void;

    /**
     * Remove refresh token
     * @param string $refreshToken
     * @return void
     */
    public function remove(string $refreshToken): void;

    /**
     * Validate refresh token
     * @param string $refreshToken
     * @return IRefreshToken|NULL
     */
    public function getValidRefreshToken(string $refreshToken): ?IRefreshToken;
}
