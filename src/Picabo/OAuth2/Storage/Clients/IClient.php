<?php

namespace Picabo\OAuth2\Storage\Clients;

/**
 * OAuth2 client entity
 * @package Picabo\OAuth2\Storage\Entity
 * @author Drahomír Hanák
 */
interface IClient
{

    /**
     * Get client id
     * @return string|int
     */
    public function getId(): string|int;

    /**
     * Get client secret code
     * @return string|int
     */
    public function getSecret(): string|int;

    /**
     * Get client redirect URL
     * @return string
     */
    public function getRedirectUrl(): string;
}
