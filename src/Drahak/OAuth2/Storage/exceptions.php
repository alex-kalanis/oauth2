<?php

namespace Drahak\OAuth2\Storage;

use Drahak\OAuth2\RuntimeException;

/**
 * TokenException is thrown when an problem with secure token appears
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
class TokenException extends RuntimeException
{
}

/**
 * InvalidAccessTokenException is thrown when token expires or when it does not exist
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
class InvalidAccessTokenException extends TokenException
{
}

/**
 * InvalidRefreshTokenException is thrown when refresh token expires or when it does not exist
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
class InvalidRefreshTokenException extends TokenException
{
}

/**
 * InvalidAuthorizationCodeException is thrown when authorization code expires or when it does not exist
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
class InvalidAuthorizationCodeException extends TokenException
{
}