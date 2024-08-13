<?php

namespace Drahak\OAuth2\Storage\AuthorizationCodes;

use Drahak\OAuth2\InvalidScopeException;

/**
 * IAuthorizationCodeStorage
 * @package Drahak\OAuth2\Storage\AuthorizationCodes
 * @author Drahomír Hanák
 */
interface IAuthorizationCodeStorage
{

    /**
     * Store authorization code
     * @param IAuthorizationCode $authorizationCode
     * @throws InvalidScopeException
     */
    public function store(IAuthorizationCode $authorizationCode);

    /**
     * Remove authorization code
     * @param string $authorizationCode
     * @return void
     */
    public function remove($authorizationCode);

    /**
     * Get valid authorization code
     * @param string $authorizationCode
     * @return IAuthorizationCode|NULL
     */
    public function getValidAuthorizationCode($authorizationCode);

}
