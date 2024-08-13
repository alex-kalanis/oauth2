<?php

namespace Drahak\OAuth2\Storage;

use Drahak\OAuth2\Storage\Clients\IClient;

/**
 * ITokenFacade
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
interface ITokenFacade
{

    /** Default token names as defined in specification */
    const ACCESS_TOKEN = 'access_token';
    const REFRESH_TOKEN = 'refresh_token';
    const AUTHORIZATION_CODE = 'authorization_code';

    /**
     * Create token
     * @param IClient $client
     * @param int $userId
     * @param array $scope
     * @return mixed
     */
    public function create(IClient $client, $userId, array $scope = array());

    /**
     * Returns token entity
     * @param string $token
     */
    public function getEntity($token);

    /**
     * Get token identifier name
     * @return string
     */
    public function getIdentifier();

}