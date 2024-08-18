<?php

namespace Picabo\OAuth2\Exceptions;

use Exception;

/**
 * InvalidRequestException
 * @package Picabo\OAuth2\Application
 * @author Drahomír Hanák
 */
class InvalidRequestException extends OAuthException
{
    protected string $key = 'invalid_request';

    public function __construct($message = 'Invalid request parameters', Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
