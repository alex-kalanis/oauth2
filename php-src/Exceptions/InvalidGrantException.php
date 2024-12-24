<?php

namespace kalanis\OAuth2\Exceptions;


use Exception;


/**
 * InvalidGrantException is thrown when provided authorization grant (authorization vode, resource owner credentials)
 * or refresh token is invalid, expired, revoked, does not match redirect URI used in authorization request
 * @package kalanis\OAuth2
 */
class InvalidGrantException extends OAuthException
{

    protected string $key = 'invalid_grant';


    /**
     * @param string $message
     * @param Exception|null $previous
     */
    public function __construct($message = 'Givent grant token is invalid or expired', Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
