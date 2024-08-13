<?php

namespace Tests\Drahak\OAuth2\Storage\AccessTokens;

require_once __DIR__ . '/../../../bootstrap.php';

use Drahak\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use Mockery;
use Tester\Assert;
use Tests\TestCase;

class AccessTokenFacadeTest extends TestCase
{

    private $storage;

    private $keyGenerator;

    /** @var AccessTokenFacade */
    private $token;

    public function testCheckInvalidToken(): void
    {
        $token = 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12';
        $this->storage->expects('getValidAccessToken')->once()->with($token)->andReturn(FALSE);
        $this->storage->expects('remove')->once()->with($token);

        Assert::throws(function () use ($token) {
            $this->token->getEntity($token);
        }, 'Drahak\OAuth2\Storage\InvalidAccessTokenException');
    }

    public function testValidToken(): void
    {
        $entity = TRUE;
        $token = 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12';
        $this->storage->expects('getValidAccessToken')->once()->with($token)->andReturn($entity);
        $result = $this->token->getEntity($token);
        Assert::same($result, $entity);
    }

    public function testCreateToken(): void
    {
        $key = '117936fc44529a174e85ca68005b';
        $scope = array('profile', 'oauth_spec');

        $client = Mockery::mock(\Drahak\OAuth2\Storage\Clients\IClient::class);
        $client->expects('getId')->once()->andReturn(1);

        $this->keyGenerator->expects('generate')->once()->andReturn($key);

        $this->storage->expects('store')->once();

        $entity = $this->token->create($client, 54, $scope);
        Assert::equal($entity->getAccessToken(), $key);
        Assert::equal($entity->getExpires()->getTimestamp() - time(), 3600);
        Assert::equal($entity->getClientId(), 1);
        Assert::equal($entity->getUserId(), 54);
        Assert::same($entity->getScope(), $scope);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = Mockery::mock(\Drahak\OAuth2\Storage\AccessTokens\IAccessTokenStorage::class);
        $this->keyGenerator = Mockery::mock(\Drahak\OAuth2\IKeyGenerator::class);
        $this->token = new AccessTokenFacade(3600, $this->keyGenerator, $this->storage);
    }
}

(new AccessTokenFacadeTest())->run();
