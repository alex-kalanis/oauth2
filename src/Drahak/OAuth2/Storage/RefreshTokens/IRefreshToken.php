<?php

namespace Drahak\OAuth2\Storage\RefreshTokens;

use DateTime;

/**
 * IRefreshToken entity
 * @package Drahak\OAuth2\Storage\RefreshTokens
 * @author Drahomír Hanák
 */
interface IRefreshToken
{

    /**
     * Get refresh token
     * @return string
     */
    public function getRefreshToken();

    /**
     * Get expire time
     * @return DateTime
     */
    public function getExpires();

    /**
     * Get client id
     * @return string|int
     */
    public function getClientId();

    /**
     * Get refresh token user ID
     * @return string|int
     */
    public function getUserId();

}