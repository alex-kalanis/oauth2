<?php

namespace Picabo\OAuth2\Storage\AuthorizationCodes;

use Picabo\OAuth2\Storage\ITokens;

/**
 * IAuthorizationCode
 * @package Picabo\OAuth2\Storage\AuthorizationCodes
 * @author Drahomír Hanák
 */
interface IAuthorizationCode extends ITokens
{

    /**
     * Get authorization code
     * @return string
     */
    public function getAuthorizationCode(): string;
}
