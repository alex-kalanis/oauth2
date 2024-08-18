<?php

namespace Picabo\OAuth2\Exceptions;

use Exception;

/**
 * UnsupportedResponseTypeException
 * @package Picabo\OAuth2
 * @author Drahomír Hanák
 */
class UnsupportedResponseTypeException extends OAuthException
{
    protected string $key = 'unsupported_response_type';

    public function __construct($message = 'Grant type not supported', Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
