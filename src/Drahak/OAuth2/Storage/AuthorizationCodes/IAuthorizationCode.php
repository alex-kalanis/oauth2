<?php

namespace Drahak\OAuth2\Storage\AuthorizationCodes;

use DateTime;

/**
 * IAuthorizationCode
 * @package Drahak\OAuth2\Storage\AuthorizationCodes
 * @author Drahomír Hanák
 */
interface IAuthorizationCode
{

    /**
     * Get authorization code
     * @return string
     */
    public function getAuthorizationCode();

    /**
     * Set expire date
     * @return DateTime
     */
    public function getExpires();

    /**
     * Get client ID
     * @return string|int
     */
    public function getClientId();

    /**
     * Get user ID
     * @return string|int
     */
    public function getUserId();

    /**
     * Get scope
     * @return array
     */
    public function getScope();

}