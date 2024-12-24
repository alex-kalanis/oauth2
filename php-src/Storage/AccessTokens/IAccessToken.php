<?php

namespace kalanis\OAuth2\Storage\AccessTokens;


use kalanis\OAuth2\Storage\ITokens;


/**
 * IAccessToken entity
 * @package kalanis\OAuth2\Storage\AccessTokens
 */
interface IAccessToken extends ITokens
{

    /**
     * Get access token
     * @return string
     */
    public function getAccessToken(): string;
}
