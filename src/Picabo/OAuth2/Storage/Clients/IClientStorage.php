<?php

namespace Picabo\OAuth2\Storage\Clients;

/**
 * Client manager interface
 * @package Picabo\OAuth2\DataSource
 * @author Drahomír Hanák
 */
interface IClientStorage
{

    /**
     * Get client data
     * @param string|int $clientId
     * @param string|null $clientSecret
     * @return IClient|null
     */
    public function getClient(string|int $clientId, ?string $clientSecret = null): ?IClient;

    /**
     * Can client use given grant type
     * @param string|int $clientId
     * @param string $grantType
     * @return bool
     */
    public function canUseGrantType(string|int $clientId, string $grantType): bool;
}
