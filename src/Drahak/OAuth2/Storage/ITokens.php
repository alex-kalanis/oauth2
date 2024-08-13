<?php

namespace Drahak\OAuth2\Storage;

use DateTime;

/**
 * ITokens
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
interface ITokens
{
    /**
     * Set expire date
     * @return DateTime
     */
    public function getExpires(): DateTime;

    /**
     * Get user ID
     * @return string|int
     */
    public function getUserId(): string|int;

    /**
     * Get client ID
     * @return string|int
     */
    public function getClientId(): string|int;

    /**
     * Get scope
     * @return array
     */
    public function getScope(): array;
}
