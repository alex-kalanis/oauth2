<?php

namespace kalanis\OAuth2\Storage\Dibi;


use DateTime;
use Dibi\Connection;
use kalanis\OAuth2\Exceptions\InvalidScopeException;
use kalanis\OAuth2\Storage\AuthorizationCodes;
use Nette\SmartObject;


/**
 * AuthorizationCode
 * @package kalanis\OAuth2\Storage\Dibi
 */
class AuthorizationCodeStorage implements AuthorizationCodes\IAuthorizationCodeStorage
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
        return 'oauth_authorization_code';
    }

    /**
     * Get scope table
     * @return string
     */
    protected function getScopeTable(): string
    {
        return 'oauth_authorization_code_scope';
    }

    /******************** IAuthorizationCodeStorage ********************/

    /**
     * Store authorization code
     * @param AuthorizationCodes\IAuthorizationCode $authorizationCode
     * @throws InvalidScopeException
     */
    public function store(AuthorizationCodes\IAuthorizationCode $authorizationCode): void
    {
        $this->context->insert($this->getTable(), array(
            'authorization_code' => $authorizationCode->getAuthorizationCode(),
            'client_id' => $authorizationCode->getClientId(),
            'user_id' => $authorizationCode->getUserId(),
            'expires_at' => $authorizationCode->getExpires()
        ))->execute();

        $this->context->begin();
        try {
            foreach ($authorizationCode->getScope() as $scope) {
                $this->context->insert($this->getScopeTable(), [
                    'authorization_code' => $authorizationCode->getAuthorizationCode(),
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
     * Remove authorization code
     * @param string $token
     * @return void
     */
    public function remove(string $token): void
    {
        $this->context->delete($this->getTable())
            ->where('authorization_code = %s', $token)
            ->execute();
    }

    /**
     * Validate authorization code
     * @param string $authorizationCode
     * @return AuthorizationCodes\IAuthorizationCode|null
     */
    public function getValidAuthorizationCode($authorizationCode): ?AuthorizationCodes\IAuthorizationCode
    {
        $row = $this->context
            ->select('*')
            ->from($this->getTable())
            ->where('authorization_code = %s', $authorizationCode)
            ->where('TIMEDIFF(expires_at, NOW()) >= 0')
            ->fetch();

        if (!$row) {
            return null;
        }

        $scopes = $this->context
            ->select('*')
            ->from($this->getScopeTable())
            ->where('authorization_code = %s', $authorizationCode)
            ->fetchPairs('scope_name');

        return new AuthorizationCodes\AuthorizationCode(
            strval($row['authorization_code']),
            new DateTime(strval($row['expires_at'])),
            is_numeric($row['client_id']) ? intval($row['client_id']) : strval($row['client_id']),
            is_null($row['user_id']) ? null : strval($row['user_id']),
            array_map('strval', array_keys($scopes))
        );
    }
}
