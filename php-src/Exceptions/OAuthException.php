<?php

namespace kalanis\OAuth2\Exceptions;


use Exception;


/**
 * OAuthException
 * @package kalanis\OAuth2\Exceptions
 */
class OAuthException extends Exception
{

    protected string $key = '';


    /**
     * Get OAuth2 exception key as defined in specification
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
