<?php

namespace kalanis\OAuth2\Storage\AuthorizationCodes;


use kalanis\OAuth2\Storage\ITokens;


/**
 * IAuthorizationCode
 * @package kalanis\OAuth2\Storage\AuthorizationCodes
 */
interface IAuthorizationCode extends ITokens
{

    /**
     * Get authorization code
     * @return string
     */
    public function getAuthorizationCode(): string;
}
