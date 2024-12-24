<?php

namespace kalanis\OAuth2\Storage\AuthorizationCodes;


use kalanis\OAuth2\Exceptions\InvalidScopeException;
use kalanis\OAuth2\Storage\ITokenStorage;


/**
 * IAuthorizationCodeStorage
 * @package kalanis\OAuth2\Storage\AuthorizationCodes
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
