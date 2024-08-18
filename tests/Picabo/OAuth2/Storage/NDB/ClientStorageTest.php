<?php

namespace Tests\Picabo\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../DatabaseTestCase.php';

use Picabo\OAuth2\Grant\IGrant;
use Picabo\OAuth2\Storage\Clients\IClient;
use Picabo\OAuth2\Storage\NDB\ClientStorage;
use Tester\Assert;
use Tests\DatabaseTestCase;

class ClientStorageTest extends DatabaseTestCase
{
    private ClientStorage $storage;

    public function testGetClientByIdAndSecret(): void
    {
        $id = 'afa233ad-5142-32';
        $secret = sha1('password');

        $client = $this->storage->getClient($id, $secret);
        Assert::true($client instanceof IClient);
        Assert::equal($client->getId(), $id);
        Assert::equal($client->getSecret(), $secret);
    }

    public function testWheneverIsUserAllowedToUseGrantType(): void
    {
        $id = 'd3a213ad-d142-11';

        $canUseGrant = $this->storage->canUseGrantType($id, IGrant::CLIENT_CREDENTIALS);
        Assert::true($canUseGrant);
    }

    public function testUserIsNotAllowedToUseGrantType(): void
    {
        $id = 'd3a213ad-d142-11';

        $canUseGrant = $this->storage->canUseGrantType($id, 'test_credentials');
        Assert::false($canUseGrant);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = new ClientStorage($this->dbExplorer);
    }
}

(new ClientStorageTest())->run();
