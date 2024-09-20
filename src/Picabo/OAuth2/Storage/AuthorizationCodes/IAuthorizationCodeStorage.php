<?php

namespace Picabo\OAuth2\Storage\AuthorizationCodes;

use Picabo\OAuth2\Exceptions\InvalidScopeException;
use Picabo\OAuth2\Storage\ITokenStorage;

/**
 * IAuthorizationCodeStorage
 * @package Picabo\OAuth2\Storage\AuthorizationCodes
 * @author Drahomír Hanák
 */
interface IAuthorizationCodeStorage extends ITokenStorage
{

    /**
     * Store authorization code
     * @throws InvalidScopeException
     * @return void
     */
    public function store(IAuthorizationCode $authorizationCode): void;

    /**
     * Get valid authorization code
     * @param string $authorizationCode
     * @return IAuthorizationCode|null
     */
    public function getValidAuthorizationCode(string $authorizationCode): ?IAuthorizationCode;

}
