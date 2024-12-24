<?php

namespace Tests\OAuth2\Storage\NDB;


require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'DatabaseTestCase.php';


use DateTime;
use kalanis\OAuth2\Storage\AuthorizationCodes\AuthorizationCode;
use kalanis\OAuth2\Storage\AuthorizationCodes\IAuthorizationCode;
use kalanis\OAuth2\Storage\NDB\AuthorizationCodeStorage;
use Nette;
use Tester\Assert;
use Tests\DatabaseTestCase;


class AuthorizationCodeStorageTest extends DatabaseTestCase
{

    private AuthorizationCodeStorage $storage;

    public function testStoreNewAuthorizationCode(): void
    {
        $entity = $this->createEntity();
        $this->storage->store($entity);

        $stored = $this->storage->getValidAuthorizationCode($entity->getAuthorizationCode());
        Assert::true($stored instanceof IAuthorizationCode);
    }

    /**
     * Create test entity
     * @param array $scope
     * @return AuthorizationCode
     */
    protected function createEntity(array $scope = []): AuthorizationCode
    {
        return new AuthorizationCode(
            hash('sha256', Nette\Utils\Random::generate()),
            new DateTime('20.1.2050'),
            'b6daa7e9-ebb7-4b97-9f4c-61615f2de94d',
            '5fcb1ca9-7372-11',
            $scope
        );
    }

    public function testThrowsInvalidScopeExceptionWhenInvalidScopeGiven(): void
    {
        $entity = $this->createEntity(['invalid_scope_access']);

        Assert::throws(function () use ($entity) {
            $this->storage->store($entity);
        }, \kalanis\OAuth2\Exceptions\InvalidScopeException::class);
    }

    public function testRemoveAuthorizationCode(): void
    {
        $entity = $this->createEntity();
        $this->storage->store($entity);
        $this->storage->remove($entity->getAuthorizationCode());
        $stored = $this->storage->getValidAuthorizationCode($entity->getAuthorizationCode());
        Assert::null($stored);
    }

    public function testGetValidAuthorizationCodeWithScope(): void
    {
        $entity = $this->createEntity(['profile']);
        $this->storage->store($entity);
        $stored = $this->storage->getValidAuthorizationCode($entity->getAuthorizationCode());
        Assert::true($stored instanceof IAuthorizationCode);
        Assert::equal($stored->getAuthorizationCode(), $entity->getAuthorizationCode());
        Assert::equal($stored->getClientId(), $entity->getClientId());
        Assert::equal($stored->getUserId(), $entity->getUserId());
        Assert::equal($stored->getScope(), $entity->getScope());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = new AuthorizationCodeStorage($this->dbExplorer);
    }
}


(new AuthorizationCodeStorageTest())->run();
