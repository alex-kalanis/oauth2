<?php

namespace Drahak\OAuth2\Storage\AuthorizationCodes;

use Drahak\OAuth2\Storage\ITokens;

/**
 * IAuthorizationCode
 * @package Drahak\OAuth2\Storage\AuthorizationCodes
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
