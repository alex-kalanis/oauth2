<?php

namespace Tests\Drahak\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../DatabaseTestCase.php';

use DateTime;
use Drahak\OAuth2\Storage\AuthorizationCodes\AuthorizationCode;
use Drahak\OAuth2\Storage\AuthorizationCodes\IAuthorizationCode;
use Drahak\OAuth2\Storage\NDB\AuthorizationCodeStorage;
use Nette;
use Tester\Assert;
use Tests\DatabaseTestCase;

class AuthorizationCodeStorageTest extends DatabaseTestCase
{

    /** @var AuthorizationCodeStorage */
    private $storage;

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
    protected function createEntity(array $scope = array()): AuthorizationCode
    {
        return new AuthorizationCode(
            hash('sha256', Nette\Utils\Strings::random()),
            new DateTime('20.1.2050'),
            'd3a213ad-d142-11',
            '5fcb1af9-d5cd-11',
            $scope
        );
    }

    public function testThrowsInvalidScopeExceptionWhenInvalidScopeGiven(): void
    {
        $entity = $this->createEntity(array('invalid_scope_access'));

        Assert::throws(function () use ($entity) {
            $this->storage->store($entity);
        }, 'Drahak\OAuth2\InvalidScopeException');
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
        $entity = $this->createEntity(array('profile'));
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
        $this->storage = new AuthorizationCodeStorage($this->selectionFactory);
    }

}

(new AuthorizationCodeStorageTest())->run();
