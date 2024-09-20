<?php

namespace Picabo\OAuth2\Exceptions;

use Exception;

/**
 * UnauthorizedClientException
 * @package Picabo\OAuth2\Application
 * @author Drahomír Hanák
 */
class UnauthorizedClientException extends OAuthException
{
    protected string $key = 'unauthorized_client';

    /**
     * @param string $message
     * @param Exception|null $previous
     */
    public function __construct($message = 'The grant type is not authorized for this client', Exception $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}
