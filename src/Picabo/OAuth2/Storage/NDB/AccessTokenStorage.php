<?php

namespace Picabo\OAuth2\Storage\NDB;

use DateTime;
use Nette\Database\Table\ActiveRow;
use Picabo\OAuth2\Exceptions\InvalidScopeException;
use Picabo\OAuth2\Storage\AccessTokens\AccessToken;
use Picabo\OAuth2\Storage\AccessTokens\IAccessToken;
use Picabo\OAuth2\Storage\AccessTokens\IAccessTokenStorage;
use Nette\Database\Explorer;
use Nette\Database\SqlLiteral;
use Nette\Database\Table\Selection;
use Nette\SmartObject;
use PDOException;

/**
 * AccessTokenStorage
 * @package Picabo\OAuth2\Storage\AccessTokens
 * @author Drahomír Hanák
 */
class AccessTokenStorage implements IAccessTokenStorage
{
    use SmartObject;

    public function __construct(
        private readonly Explorer $context
    )
    {
    }

    /**
     * Store access token
     * @throws InvalidScopeException
     */
    public function store(IAccessToken $accessToken): void
    {
        $connection = $this->context->getConnection();
        $connection->beginTransaction();
        $this->getTable()->insert([
            'access_token' => $accessToken->getAccessToken(),
            'client_id' => $accessToken->getClientId(),
            'user_id' => $accessToken->getUserId(),
            'expires_at' => $accessToken->getExpires(),
        ]);

        try {
            foreach ($accessToken->getScope() as $scope) {
                $this->getScopeTable()->insert([
                    'access_token' => $accessToken->getAccessToken(),
                    'scope_name' => $scope,
                ]);
            }
        } catch (PDOException $e) {
            // MySQL error 1452 - Cannot add or update a child row: a foreign key constraint fails
            if (in_array(1452, (array) $e->errorInfo)) {
                throw new InvalidScopeException;
            }
            throw $e;
        }
        $connection->commit();
    }

    /**
     * Get authorization code table
     * @return Selection<ActiveRow>
     */
    protected function getTable(): Selection
    {
        return $this->context->table('oauth_access_token');
    }

    /******************** IAccessTokenStorage ********************/
    /**
     * Get scope table
     * @return Selection<ActiveRow>
     */
    protected function getScopeTable(): Selection
    {
        return $this->context->table('oauth_access_token_scope');
    }

    /**
     * Remove access token
     * @param string $accessToken
     */
    public function remove(string $accessToken): void
    {
        $this->getTable()->where(['access_token' => $accessToken])->delete();
    }

    /**
     * Get valid access token
     * @param string $accessToken
     * @return IAccessToken|null
     * @throws \Exception
     */
    public function getValidAccessToken(string $accessToken): ?IAccessToken
    {
        $row = $this->getTable()
            ->where(['access_token' => $accessToken])
            ->where(new SqlLiteral('TIMEDIFF(expires_at, NOW()) >= 0'))
            ->fetch();

        if (!$row) {
            return null;
        }

        $scopes = $this->getScopeTable()
            ->where(['access_token' => $accessToken])
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
