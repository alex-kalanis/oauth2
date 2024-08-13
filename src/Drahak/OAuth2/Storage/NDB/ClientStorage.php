<?php

namespace Drahak\OAuth2\Storage\NDB;

use Drahak\OAuth2\Storage\Clients\Client;
use Drahak\OAuth2\Storage\Clients\IClient;
use Drahak\OAuth2\Storage\Clients\IClientStorage;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

/**
 * Nette database client storage
 * @package Drahak\OAuth2\Storage\Clients
 * @author Drahomír Hanák
 */
class ClientStorage implements IClientStorage
{
    use SmartObject;

    /** @var Explorer */
    private $context;

    public function __construct(Explorer $context)
    {
        $this->context = $context;
    }

    /**
     * Find client by ID and/or secret key
     * @param string $clientId
     * @param string|null $clientSecret
     * @return IClient
     */
    public function getClient($clientId, $clientSecret = NULL)
    {
        if (!$clientId) return NULL;

        $selection = $this->getTable()->where(array('client_id' => $clientId));
        if ($clientSecret) {
            $selection->where(array('secret' => $clientSecret));
        }
        $data = $selection->fetch();
        if (!$data) return NULL;
        return new Client($data['client_id'], $data['secret'], $data['redirect_url']);
    }

    /**
     * Get client table selection
     * @return Selection
     */
    protected function getTable()
    {
        return $this->context->table('oauth_client');
    }

    /**
     * Can client use given grant type
     * @param string $clientId
     * @param string $grantType
     * @return bool
     */
    public function canUseGrantType($clientId, $grantType)
    {
        $result = $this->getTable()->getConnection()->query('
			SELECT g.name
			FROM oauth_client_grant AS cg
			RIGHT JOIN oauth_grant AS g ON cg.grant_id = cg.grant_id AND g.name = ?
			WHERE cg.client_id = ?
		', $grantType, $clientId);
        return (bool)$result->fetch();
    }
}