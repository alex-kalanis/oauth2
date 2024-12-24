<?php

namespace kalanis\OAuth2\Exceptions;


use Exception;


/**
 * InvalidRequestException
 * @package kalanis\OAuth2\Application
 */
class InvalidRequestException extends OAuthException
{

    protected string $key = 'invalid_request';


    /**
     * @param string $message
     * @param Exception|null $previous
     */
    public function __construct($message = 'Invalid request parameters', Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
