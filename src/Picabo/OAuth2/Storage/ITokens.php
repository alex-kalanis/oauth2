<?php

namespace Picabo\OAuth2\Storage;

use DateTime;

/**
 * ITokens
 * @package Picabo\OAuth2\Token
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
     * @return string|int|null
     */
    public function getUserId(): string|int|null;

    /**
     * Get client ID
     * @return string|int
     */
    public function getClientId(): string|int;

    /**
     * Get scope
     * @return array<string>
     */
    public function getScope(): array;
}
