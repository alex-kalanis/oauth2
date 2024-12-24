<?php

namespace kalanis\OAuth2\Storage\RefreshTokens;


use kalanis\OAuth2\Storage\ITokens;


/**
 * IRefreshToken entity
 * @package kalanis\OAuth2\Storage\RefreshTokens
 */
interface IRefreshToken extends ITokens
{

    /**
     * Get refresh token
     * @return string
     */
    public function getRefreshToken(): string;
}
