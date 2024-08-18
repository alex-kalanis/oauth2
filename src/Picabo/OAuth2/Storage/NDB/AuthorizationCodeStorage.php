<?php

namespace Picabo\OAuth2\Storage\NDB;

use DateTime;
use Picabo\OAuth2\Exceptions\InvalidScopeException;
use Picabo\OAuth2\Storage\AuthorizationCodes;
use Picabo\OAuth2\Storage\AuthorizationCodes\AuthorizationCode;
use Nette\Database\Explorer;
use Nette\Database\SqlLiteral;
use Nette\Database\Table\Selection;
use Nette\SmartObject;
use PDOException;

/**
 * AuthorizationCode
 * @package Picabo\OAuth2\Storage\AuthorizationCodes
 * @author Drahomír Hanák
 */
class AuthorizationCodeStorage implements AuthorizationCodes\IAuthorizationCodeStorage
{
    use SmartObject;

    public function __construct(
        private readonly Explorer $context
    )
    {
    }

    /**
     * Store authorization code
     * @throws InvalidScopeException
     */
    public function store(AuthorizationCodes\IAuthorizationCode $authorizationCode): void
    {
        $this->getTable()->insert([
            'authorization_code' => $authorizationCode->getAuthorizationCode(),
            'client_id' => $authorizationCode->getClientId(),
            'user_id' => $authorizationCode->getUserId(),
            'expires_at' => $authorizationCode->getExpires(),
        ]);

        $connection = $this->context->getConnection();
        $connection->beginTransaction();
        try {
            foreach ($authorizationCode->getScope() as $scope) {
                $this->getScopeTable()->insert([
                    'authorization_code' => $authorizationCode->getAuthorizationCode(),
                    'scope_name' => $scope,
                ]);
            }
        } catch (PDOException $e) {
            // MySQL error 1452 - Cannot add or update a child row: a foreign key constraint fails
            if (in_array(1452, $e->errorInfo)) {
                throw new InvalidScopeException;
            }
            throw $e;
        }
        $connection->commit();
    }

    /**
     * Get authorization code table
     */
    protected function getTable(): Selection
    {
        return $this->context->table('oauth_authorization_code');
    }

    /******************** IAuthorizationCodeStorage ********************/
    /**
     * Get scope table
     */
    protected function getScopeTable(): Selection
    {
        return $this->context->table('oauth_authorization_code_scope');
    }

    /**
     * Remove authorization code
     * @param string $authorizationCode
     * @return void
     */
    public function remove(string $authorizationCode): void
    {
        $this->getTable()->where(['authorization_code' => $authorizationCode])->delete();
    }

    /**
     * Validate authorization code
     * @param string $authorizationCode
     * @return AuthorizationCodes\IAuthorizationCode|null
     */
    public function getValidAuthorizationCode(string $authorizationCode): ?AuthorizationCodes\IAuthorizationCode
    {
        $row = $this->getTable()
            ->where(['authorization_code' => $authorizationCode])
            ->where(new SqlLiteral('TIMEDIFF(expires_at, NOW()) >= 0'))
            ->fetch();

        if (!$row) {
            return null;
        }

        $scopes = $this->getScopeTable()
            ->where(['authorization_code' => $authorizationCode])
            ->fetchPairs('scope_name');

        return new AuthorizationCodes\AuthorizationCode(
            $row['authorization_code'],
            new DateTime($row['expires_at']),
            $row['client_id'],
            $row['user_id'],
            array_keys($scopes)
        );
    }
}
