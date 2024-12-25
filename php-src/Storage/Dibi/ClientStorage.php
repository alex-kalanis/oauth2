<?php

namespace kalanis\OAuth2\Storage\Dibi;


use Dibi\Connection;
use kalanis\OAuth2\Storage\Clients\Client;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\Clients\IClientStorage;
use Nette\SmartObject;


/**
 * Nette database client storage
 * @package kalanis\OAuth2\Storage\Dibi
 */
class ClientStorage implements IClientStorage
{

    use SmartObject;


    public function __construct(
        protected readonly Connection $context,
    )
    {
    }

    /**
     * Get client table selection
     * @return string
     */
    protected function getTable(): string
    {
        return 'oauth_client';
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

        $selection = $this->context
            ->select('*')
            ->from($this->getTable())
            ->where('client_id = %s', $clientId);
        if ($clientSecret) {
            $selection->where('secret = %s', $clientSecret);
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
			RIGHT JOIN oauth_grant AS g ON cg.grant_id = cg.grant_id AND g.name = %s
			WHERE cg.client_id = %i
		', $grantType, $clientId);
        return !empty($result->fetch());
    }
}
