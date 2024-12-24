<?php

namespace kalanis\OAuth2\Storage\Dibi;


use DateTime;
use Dibi\Connection;
use kalanis\OAuth2\Storage\RefreshTokens\IRefreshToken;
use kalanis\OAuth2\Storage\RefreshTokens\IRefreshTokenStorage;
use kalanis\OAuth2\Storage\RefreshTokens\RefreshToken;
use Nette\SmartObject;


/**
 * Nette database RefreshToken storage
 * @package kalanis\OAuth2\Storage\RefreshTokens
 */
class RefreshTokenStorage implements IRefreshTokenStorage
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
        return 'oauth_refresh_token';
    }

    /******************** IRefreshTokenStorage ********************/

    /**
     * Store refresh token
     * @param IRefreshToken $refreshToken
     */
    public function store(IRefreshToken $refreshToken): void
    {
        $this->context->insert($this->getTable(), array(
            'refresh_token' => $refreshToken->getRefreshToken(),
            'client_id' => $refreshToken->getClientId(),
            'user_id' => $refreshToken->getUserId(),
            'expires_at' => $refreshToken->getExpires()
        ))->execute();
    }

    /**
     * Remove refresh token
     * @param string $token
     */
    public function remove(string $token): void
    {
        $this->context->delete($this->getTable())->where(array('refresh_token' => $token))->execute();
    }

    /**
     * Get valid refresh token
     * @param string $refreshToken
     * @return IRefreshToken|null
     */
    public function getValidRefreshToken(string $refreshToken): ?IRefreshToken
    {
        $row = $this->context->select('*')->from($this->getTable())
            ->where('refresh_token = %s', $refreshToken)
            ->where('TIMEDIFF(expires_at, NOW()) >= 0')
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
