<?php

namespace kalanis\OAuth2\Storage\NDB;


use kalanis\OAuth2\Storage\Clients\Client;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\Clients\IClientStorage;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


/**
 * Nette database client storage
 * @package kalanis\OAuth2\Storage\NDB
 */
class ClientStorage implements IClientStorage
{

    public function __construct(
        private readonly Explorer $context,
    )
    {
    }

    /**
     * Find client by ID and/or secret key
     * @param string|int $clientId
     * @param string|null $clientSecret
     * @return IClient|null
     */
    public function getClient(string|int $clientId, #[\SensitiveParameter] string|null $clientSecret = null): ?IClient
    {
        if (!$clientId) {
            return null;
        }

        $selection = $this->getTable()->where(['client_id' => $clientId]);
        if ($clientSecret) {
            $selection->where(['secret' => $clientSecret]);
        }
        $data = $selection->fetch();
        if (!$data) {
            return null;
        }
        return new Client(
            is_numeric($data['client_id']) ? intval($data['client_id']) : strval($data['client_id']),
            strval($data['secret']),
            strval($data['redirect_url']),
        );
    }

    /**
     * Get client table selection
     * @return Selection<ActiveRow>
     */
    protected function getTable(): Selection
    {
        return $this->context->table('oauth_client');
    }

    /**
     * Can client use given grant type
     * @param string|int $clientId
     * @param string $grantType
     * @return bool
     */
    public function canUseGrantType(string|int $clientId, string $grantType): bool
    {
        $result = $this->context->query('
			SELECT g.name
			FROM oauth_client_grant AS cg
			RIGHT JOIN oauth_grant AS g ON g.grant_id = cg.grant_id AND g.name = ?
			WHERE cg.client_id = ?
		', $grantType, $clientId);
        return !empty($result->fetch());
    }
}
