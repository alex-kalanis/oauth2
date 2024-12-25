<?php

namespace kalanis\OAuth2\Storage\Dibi;


use DateTime;
use Dibi\Connection;
use kalanis\OAuth2\Exceptions\InvalidScopeException;
use kalanis\OAuth2\Storage\AccessTokens\AccessToken;
use kalanis\OAuth2\Storage\AccessTokens\IAccessToken;
use kalanis\OAuth2\Storage\AccessTokens\IAccessTokenStorage;
use Nette\SmartObject;


/**
 * AccessTokenStorage
 * @package kalanis\OAuth2\Storage\Dibi
 */
class AccessTokenStorage implements IAccessTokenStorage
{

    use SmartObject;


    public function __construct(
        private readonly Connection $context,
    )
    {
    }

    /**
     * Get authorization code table
     * @return string
     */
    protected function getTable(): string
    {
        return 'oauth_access_token';
    }

    /**
     * Get scope table
     * @return string
     */
    protected function getScopeTable(): string
    {
        return 'oauth_access_token_scope';
    }

    /******************** IAccessTokenStorage ********************/

    /**
     * Store access token
     * @param IAccessToken $accessToken
     * @throws InvalidScopeException
     */
    public function store(IAccessToken $accessToken): void
    {
        $this->context->begin();
        $this->context->insert($this->getTable(), [
            'access_token' => $accessToken->getAccessToken(),
            'client_id' => $accessToken->getClientId(),
            'user_id' => $accessToken->getUserId(),
            'expires_at' => $accessToken->getExpires(),
        ])->execute();

        try {
            foreach ($accessToken->getScope() as $scope) {
                $this->context->insert($this->getScopeTable(), [
                    'access_token' => $accessToken->getAccessToken(),
                    'scope_name' => $scope,
                ])->execute();
            }
        } catch (\PDOException $e) {
            // MySQL error 1452 - Cannot add or update a child row: a foreign key constraint fails
            if (in_array(1452, (array) $e->errorInfo)) {
                throw new InvalidScopeException;
            }
            throw $e;
        }
        $this->context->commit();
    }

    /**
     * Remove access token
     * @param string $token
     */
    public function remove(string $token): void
    {
        $this->context->delete($this->getTable())
            ->where('access_token = %s', $token)
            ->execute();
    }

    /**
     * Get valid access token
     * @param string $accessToken
     * @return IAccessToken|NULL
     */
    public function getValidAccessToken(string $accessToken): ?IAccessToken
    {
        $row = $this->context
            ->select('*')
            ->from($this->getTable())
            ->where('access_token = %s', $accessToken)
            ->where('TIMEDIFF(expires_at, NOW()) >= 0')
            ->fetch();

        if (!$row) {
            return null;
        }

        $scopes = $this->context
            ->select('*')
            ->from($this->getScopeTable())
            ->where('access_token = %s', $accessToken)
            ->fetchPairs('scope_name');

        return new AccessToken(
            strval($row['access_token']),
            new DateTime(strval($row['expires_at'])),
            is_numeric($row['client_id'])? intval($row['client_id']) : strval($row['client_id']),
            is_null($row['user_id']) ? null : strval($row['user_id']),
            array_map('strval', array_keys($scopes))
        );
    }
}
