<?php

namespace Drahak\OAuth2;

use Exception;

/**
 * LogicException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class LogicException extends \LogicException
{
}

/**
 * InvalidArgumentException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class InvalidArgumentException extends LogicException
{
}

/**
 * NotImplementedException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class NotImplementedException extends LogicException
{
}

/**
 * RuntimeException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class RuntimeException extends \RuntimeException
{
}

/**
 * InvalidStateException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class InvalidStateException extends RuntimeException
{
}

/**
 * UnsupportedOperationException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class UnsupportedOperationException extends LogicException
{
}

/**
 * NotLoggedInException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class NotLoggedInException extends LogicException
{
}


/**
 * OAuthException
 * @package Drahak\OAuth2\Application
 * @author Drahomír Hanák
 */
class OAuthException extends Exception
{

    /** @var string */
    protected $key;

    /**
     * Get OAuth2 exception key as defined in specification
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

}

/**
 * InvalidRequestException
 * @package Drahak\OAuth2\Application
 * @author Drahomír Hanák
 */
class InvalidRequestException extends OAuthException
{

    /** @var string */
    protected $key = 'invalid_request';

    public function __construct($message = 'Invalid request parameters', Exception $previous = NULL)
    {
        parent::__construct($message, 400, $previous);
    }

}

/**
 * UnsupportedResponseTypeException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class UnsupportedResponseTypeException extends OAuthException
{

    /** @var string */
    protected $key = 'unsupported_response_type';

    public function __construct($message = 'Grant type not supported', Exception $previous = NULL)
    {
        parent::__construct($message, 400, $previous);
    }

}

/**
 * ÜnauthorizedClientException
 * @package Drahak\OAuth2\Application
 * @author Drahomír Hanák
 */
class UnauthorizedClientException extends OAuthException
{

    /** @var string */
    protected $key = 'unauthorized_client';

    public function __construct($message = 'The grant type is not authorized for this client', Exception $previous = NULL)
    {
        parent::__construct($message, 401, $previous);
    }

}


/**
 * InvalidScopeException
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class InvalidScopeException extends OAuthException
{

    /** @var string */
    protected $key = 'invalid_scope';

    public function __construct($message = 'Given scope does not exist', Exception $previous = NULL)
    {
        parent::__construct($message, 400, $previous);
    }

}

/**
 * InvalidGrantException is thrown when provided authorization grant (authorization vode, resource owner credentials)
 * or refresh token is invalid, expired, revoked, does not match redirect URI used in authorization request
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
class InvalidGrantException extends OAuthException
{

    /** @var string */
    protected $key = 'invalid_grant';

    public function __construct($message = 'Givent grant token is invalid or expired', Exception $previous = NULL)
    {
        parent::__construct($message, 400, $previous);
    }

}