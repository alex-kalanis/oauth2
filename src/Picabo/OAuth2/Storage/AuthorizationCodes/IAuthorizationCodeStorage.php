<?php

namespace Picabo\OAuth2\Storage\AuthorizationCodes;

use Picabo\OAuth2\Exceptions\InvalidScopeException;

/**
 * IAuthorizationCodeStorage
 * @package Picabo\OAuth2\Storage\AuthorizationCodes
 * @author Drahomír Hanák
 */
interface IAuthorizationCodeStorage
{

    /**
     * Store authorization code
     * @throws InvalidScopeException
     */
    public function store(IAuthorizationCode $authorizationCode);

    /**
     * Remove authorization code
     * @param string $authorizationCode
     * @return void
     */
    public function remove(string $authorizationCode): void;

    /**
     * Get valid authorization code
     * @param string $authorizationCode
     * @return IAuthorizationCode|null
     */
    public function getValidAuthorizationCode(string $authorizationCode): ?IAuthorizationCode;

}
