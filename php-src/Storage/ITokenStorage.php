<?php

namespace kalanis\OAuth2\Storage;


use kalanis\OAuth2\Storage\Exceptions\TokenException;


/**
 * ITokenStorage
 * @package kalanis\OAuth2\Token
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
