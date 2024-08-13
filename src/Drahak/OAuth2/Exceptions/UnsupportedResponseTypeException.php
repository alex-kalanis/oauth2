<?php

namespace Drahak\OAuth2\Exceptions;

use Exception;

/**
 * UnsupportedResponseTypeException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class UnsupportedResponseTypeException extends OAuthException
{
    protected string $key = 'unsupported_response_type';

    public function __construct($message = 'Grant type not supported', Exception $previous = NULL)
    {
        parent::__construct($message, 400, $previous);
    }
}
