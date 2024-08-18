<?php

namespace Picabo\OAuth2\Exceptions;

use Exception;

/**
 * OAuthException
 * @package Picabo\OAuth2\Application
 * @author Drahomír Hanák
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
