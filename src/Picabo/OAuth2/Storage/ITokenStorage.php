<?php

namespace Picabo\OAuth2\Storage;

use Picabo\OAuth2\Storage\Exceptions\TokenException;

/**
 * ITokenStorage
 * @package Picabo\OAuth2\Token
 */
interface ITokenStorage
{
    /**
     * Remove by token
     * @param string $token
     * @throws TokenException
     * @return void
     */
    public function remove(string $token): void;
}
