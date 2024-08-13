<?php

namespace Tests\Drahak\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../DatabaseTestCase.php';

use DateTime;
use Drahak\OAuth2\Storage\NDB\RefreshTokenStorage;
use Drahak\OAuth2\Storage\RefreshTokens\IRefreshToken;
use Drahak\OAuth2\Storage\RefreshTokens\RefreshToken;
use Nette;
use Tester\Assert;
use Tests\DatabaseTestCase;

/**
 * Test: Tests\Drahak\OAuth2\Storage\NDB\RefreshTokenStorage.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\NDB\RefreshTokenStorageTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\NDB
 */
class RefreshTokenStorageTest extends DatabaseTestCase
{

    /** @var RefreshTokenStorage */
    private $storage;

    public function testCreateRefreshToken(): void
    {
        $entity = $this->createEntity();
        $this->storage->store($entity);

        $stored = $this->storage->getValidRefreshToken($entity->getRefreshToken());
        Assert::true($stored instanceof IRefreshToken);
    }

    /**
     * Create test entity
     * @param string|null $userId
     * @return RefreshToken
     */
    protected function createEntity(string|null $userId = NULL): RefreshToken
    {
        return new RefreshToken(
            hash('sha256', Nette\Utils\Strings::random()),
            new DateTime('20.1.2050'),
            'd3a213ad-d142-11',
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
        $this->storage = new RefreshTokenStorage($this->selectionFactory);
    }

}

(new RefreshTokenStorageTest())->run();
