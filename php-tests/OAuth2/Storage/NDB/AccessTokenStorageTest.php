<?php

namespace Tests\OAuth2\Storage\NDB;


require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'DatabaseTestCase.php';


use DateTime;
use kalanis\OAuth2\Storage\AccessTokens\AccessToken;
use kalanis\OAuth2\Storage\AccessTokens\IAccessToken;
use kalanis\OAuth2\Storage\NDB\AccessTokenStorage;
use Nette;
use Tester\Assert;
use Tests\DatabaseTestCase;


class AccessTokenStorageTest extends DatabaseTestCase
{

    private AccessTokenStorage $storage;

    public function testCreateAccessToken(): void
    {
        $entity = $this->createEntity();
        $this->storage->store($entity);

        $stored = $this->storage->getValidAccessToken($entity->getAccessToken());
        Assert::true($stored instanceof IAccessToken);
    }

    /**
     * Create test entity
     * @param string|null $userId
     * @param array $scope
     * @return AccessToken
     */
    protected function createEntity(string|null $userId = null, array $scope = []): AccessToken
    {
        return new AccessToken(
            hash('sha256', Nette\Utils\Random::generate()),
            new DateTime('20.1.2050'),
            'b6daa7e9-ebb7-4b97-9f4c-61615f2de94d',
            $userId,
            $scope
        );
    }

    public function testRemoveAccessToken(): void
    {
        $entity = $this->createEntity();
        $this->storage->store($entity);
        $this->storage->remove($entity->getAccessToken());
        $stored = $this->storage->getValidAccessToken($entity->getAccessToken());
        Assert::null($stored);
    }

    public function testThrowsInvalidScopeExceptionWhenInvalidScopeGiven(): void
    {
        $entity = $this->createEntity('5fcb1af9-d5cd-11', ['invalid_scope_access']);

        Assert::throws(function () use ($entity) {
            $this->storage->store($entity);
        }, \kalanis\OAuth2\Exceptions\InvalidScopeException::class);
    }

    public function testGetValidAccessTokenEntityWithScope(): void
    {
        $entity = $this->createEntity('5fcb1af9-d5cd-11', ['profile']);
        $this->storage->store($entity);
        $stored = $this->storage->getValidAccessToken($entity->getAccessToken());
        Assert::true($stored instanceof IAccessToken);
        Assert::equal($stored->getAccessToken(), $entity->getAccessToken());
        Assert::equal($stored->getClientId(), $entity->getClientId());
        Assert::equal($stored->getUserId(), $entity->getUserId());
        Assert::equal($stored->getScope(), $entity->getScope());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = new AccessTokenStorage($this->dbExplorer);
    }
}


(new AccessTokenStorageTest())->run();
