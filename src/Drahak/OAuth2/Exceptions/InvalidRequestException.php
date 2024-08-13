<?php

namespace Drahak\OAuth2\Exceptions;

use Exception;

/**
 * InvalidRequestException
 * @package Drahak\OAuth2\Application
 * @author Drahomír Hanák
 */
class InvalidRequestException extends OAuthException
{
    protected string $key = 'invalid_request';

    public function __construct($message = 'Invalid request parameters', Exception $previous = NULL)
    {
        parent::__construct($message, 400, $previous);
    }
}
