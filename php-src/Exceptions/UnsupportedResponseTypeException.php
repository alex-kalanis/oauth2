<?php

namespace kalanis\OAuth2\Exceptions;


use Exception;


/**
 * UnsupportedResponseTypeException
 * @package kalanis\OAuth2\Exceptions
 */
class UnsupportedResponseTypeException extends OAuthException
{

    protected string $key = 'unsupported_response_type';


    /**
     * @param string $message
     * @param Exception|null $previous
     */
    public function __construct($message = 'Grant type not supported', Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
