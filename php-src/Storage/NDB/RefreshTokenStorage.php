<?php

namespace kalanis\OAuth2\Storage\NDB;


use DateTime;
use kalanis\OAuth2\Storage\RefreshTokens\IRefreshToken;
use kalanis\OAuth2\Storage\RefreshTokens\IRefreshTokenStorage;
use kalanis\OAuth2\Storage\RefreshTokens\RefreshToken;
use Nette\Database\Explorer;
use Nette\Database\SqlLiteral;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


/**
 * Nette database RefreshToken storage
 * @package kalanis\OAuth2\Storage\NDB
 */
class RefreshTokenStorage implements IRefreshTokenStorage
{
    public function __construct(
        private readonly Explorer $context,
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
     * @param string $token
     */
    public function remove(string $token): void
    {
        $this->getTable()->where(['refresh_token' => $token])->delete();
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
