<?php

namespace Tests\OAuth2\Storage\NDB;


require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'DatabaseTestCase.php';


use kalanis\OAuth2\Grant\IGrant;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\NDB\ClientStorage;
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
