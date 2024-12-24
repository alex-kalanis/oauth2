<?php

namespace kalanis\OAuth2\Exceptions;

use Exception;


/**
 * InvalidScopeException
 * @package kalanis\OAuth2
 */
class InvalidScopeException extends OAuthException
{

    protected string $key = 'invalid_scope';


    /**
     * @param string $message
     * @param Exception|null $previous
     */
    public function __construct($message = 'Given scope does not exist', Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
