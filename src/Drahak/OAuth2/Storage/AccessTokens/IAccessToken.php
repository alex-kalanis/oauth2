<?php

namespace Drahak\OAuth2\Storage\AccessTokens;

use DateTime;

/**
 * IAccessToken entity
 * @package Drahak\OAuth2\Storage\AccessTokens
 * @author Drahomír Hanák
 */
interface IAccessToken
{

    /**
     * Get access token
     * @return string
     */
    public function getAccessToken();

    /**
     * Get expires time
     * @return DateTime
     */
    public function getExpires();

    /**
     * Get client ID
     * @return string|int
     */
    public function getClientId();

    /**
     * Get access token user ID
     * @return string|int
     */
    public function getUserId();

    /**
     * Get scope
     * @return array
     */
    public function getScope();

}