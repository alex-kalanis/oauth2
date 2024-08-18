<?php

namespace Tests\Picabo\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../DatabaseTestCase.php';

use DateTime;
use Picabo\OAuth2\Storage\NDB\RefreshTokenStorage;
use Picabo\OAuth2\Storage\RefreshTokens\IRefreshToken;
use Picabo\OAuth2\Storage\RefreshTokens\RefreshToken;
use Nette;
use Tester\Assert;
use Tests\DatabaseTestCase;

class RefreshTokenStorageTest extends DatabaseTestCase
{

    /** @var RefreshTokenStorage */
    private RefreshTokenStorage $storage;

    public function testCreateRefreshToken(): void
    {
        $entity = $this->createEntity();
        $this->storage->store($entity);

        $stored = $this->storage->getValidRefreshToken($entity->getRefreshToken());
        Assert::true($stored instanceof IRefreshToken);
    }

    /**
     * Create test entity
     * @param string|int|null $userId
     * @return RefreshToken
     */
    protected function createEntity(string|int|null $userId = null): RefreshToken
    {
        return new RefreshToken(
            hash('sha256', Nette\Utils\Random::generate()),
            new DateTime('20.1.2050'),
            'd3a213ad-7b7a-11',
            $userId
        );
    }

    public function testRemoveRefreshToken(): void
    {
        $entity = $this->createEntity();
        $this->storage->store($entity);
        $this->storage->remove($entity->getRefreshToken());
        $stored = $this->storage->getValidRefreshToken($entity->getRefreshToken());
        Assert::null($stored);
    }

    public function testGetValidRefreshToken(): void
    {
        $entity = $this->createEntity('5fcb1af9-d5cd-11');
        $this->storage->store($entity);
        $stored = $this->storage->getValidRefreshToken($entity->getRefreshToken());
        Assert::true($stored instanceof IRefreshToken);
        Assert::equal($stored->getRefreshToken(), $entity->getRefreshToken());
        Assert::equal($stored->getClientId(), $entity->getClientId());
        Assert::equal($stored->getUserId(), $entity->getUserId());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = new RefreshTokenStorage($this->dbExplorer);
    }

}

(new RefreshTokenStorageTest())->run();
