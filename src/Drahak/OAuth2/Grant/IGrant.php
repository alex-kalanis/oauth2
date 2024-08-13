<?php

namespace Drahak\OAuth2\Grant;

/**
 * Grant type interface
 * @package Drahak\OAuth2\Grant
 * @author Drahomír Hanák
 */
interface IGrant
{

    /** Grant types defined in specification */
    public const AUTHORIZATION_CODE = 'authorization_code';
    public const CLIENT_CREDENTIALS = 'client_credentials';
    public const REFRESH_TOKEN = 'refresh_token';
    public const IMPLICIT = 'implicit';
    public const PASSWORD = 'password';

    /**
     * Get identifier string to this grant type
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Get access token
     * @return array
     */
    public function getAccessToken(): array;
}
