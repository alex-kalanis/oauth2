<?php

namespace Picabo\OAuth2\Storage\NDB;

use DateTime;
use Nette\Database\Table\ActiveRow;
use Picabo\OAuth2\Storage\RefreshTokens\IRefreshToken;
use Picabo\OAuth2\Storage\RefreshTokens\IRefreshTokenStorage;
use Picabo\OAuth2\Storage\RefreshTokens\RefreshToken;
use Nette\Database\Explorer;
use Nette\Database\SqlLiteral;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

/**
 * Nette database RefreshToken storage
 * @package Picabo\OAuth2\Storage\RefreshTokens
 * @author Drahomír Hanák
 */
class RefreshTokenStorage implements IRefreshTokenStorage
{
    use SmartObject;

    public function __construct(
        private readonly Explorer $context
    )
    {
    }

    /**
     * Store refresh token
     */
    public function store(IRefreshToken $refreshToken): void
    {
        $this->getTable()->insert([
            'refresh_token' => $refreshToken->getRefreshToken(),
            'client_id' => $refreshToken->getClientId(),
            'user_id' => $refreshToken->getUserId(),
            'expires_at' => $refreshToken->getExpires(),
        ]);
    }

    /******************** IRefreshTokenStorage ********************/
    /**
     * Get authorization code table
     * @return Selection<ActiveRow>
     */
    protected function getTable(): Selection
    {
        return $this->context->table('oauth_refresh_token');
    }

    /**
     * Remove refresh token
     * @param string $refreshToken
     */
    public function remove(string $refreshToken): void
    {
        $this->getTable()->where(['refresh_token' => $refreshToken])->delete();
    }

    /**
     * Get valid refresh token
     * @param string $refreshToken
     * @return IRefreshToken|null
     */
    public function getValidRefreshToken(string $refreshToken): ?IRefreshToken
    {
        $row = $this->getTable()
            ->where(['refresh_token' => $refreshToken])
            ->where(new SqlLiteral('TIMEDIFF(expires_at, NOW()) >= 0'))
            ->fetch();

        if (!$row) {
            return null;
        }

        return new RefreshToken(
            strval($row['refresh_token']),
            new DateTime(strval($row['expires_at'])),
            is_numeric($row['client_id'])? intval($row['client_id']) : strval($row['client_id']),
            is_null($row['user_id']) ? null : strval($row['user_id']),
        );
    }
}
