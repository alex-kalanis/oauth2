<?php

namespace Drahak\OAuth2\Storage\Clients;

/**
 * OAuth2 client entity
 * @package Drahak\OAuth2\Storage\Entity
 * @author Drahomír Hanák
 */
interface IClient
{

    /**
     * Get client id
     * @return string|int
     */
    public function getId();

    /**
     * Get client secret code
     * @return string|int
     */
    public function getSecret();

    /**
     * Get client redirect URL
     * @return string
     */
    public function getRedirectUrl();

}