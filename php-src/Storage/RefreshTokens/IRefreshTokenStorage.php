<?php

namespace kalanis\OAuth2\Storage\RefreshTokens;


use kalanis\OAuth2\Storage\ITokenStorage;


/**
 * IRefreshTokenStorage
 * @package kalanis\OAuth2\Storage\RefreshTokens
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
