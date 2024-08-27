<?php

namespace Picabo\OAuth2\Grant;

use Picabo\OAuth2\Grant\Exceptions\InvalidGrantTypeException;
use Picabo\OAuth2\Http\IInput;
use Picabo\OAuth2\Storage\Clients\IClient;
use Picabo\OAuth2\Storage\Clients\IClientStorage;
use Picabo\OAuth2\Storage\TokenContext;
use Picabo\OAuth2\Exceptions\UnauthorizedClientException;
use Nette\Security\User;
use Nette\SmartObject;

/**
 * GrantType
 * @package Picabo\OAuth2\Grant
 * @author Drahomír Hanák
 *
 * @property-read string $identifier
 */
abstract class GrantType implements IGrant
{
    use SmartObject;

    public const SCOPE_KEY = 'scope';
    public const CLIENT_ID_KEY = 'client_id';
    public const CLIENT_SECRET_KEY = 'client_secret';
    public const GRANT_TYPE_KEY = 'grant_type';

    private ?IClient $client = null;

    public function __construct(
        protected IInput $input,
        protected TokenContext $token,
        protected IClientStorage $clientStorage,
        protected User $user,
    )
    {
    }

    /**
     * Get access token
     * @throws UnauthorizedClientException
     * @throws InvalidGrantTypeException
     * @return array
     */
    public final function getAccessToken(): array
    {
        if (!$this->getClient()) {
            throw new UnauthorizedClientException('Client is not found');
        }

        $this->verifyGrantType();
        $this->verifyRequest();
        return $this->generateAccessToken();
    }

    /**
     * Get client
     * @return IClient
     */
    protected function getClient(): IClient
    {
        if (!$this->client) {
            $clientId = $this->input->getParameter(self::CLIENT_ID_KEY);
            $clientSecret = $this->input->getParameter(self::CLIENT_SECRET_KEY);
            $this->client = $this->clientStorage->getClient($clientId, $clientSecret);
        }
        return $this->client;
    }

    /****************** IGrant interface ******************/

    /**
     * Verify grant type
     * @throws UnauthorizedClientException
     * @throws InvalidGrantTypeException
     */
    protected function verifyGrantType(): void
    {
        $grantType = $this->input->getParameter(self::GRANT_TYPE_KEY);
        if (!$grantType) {
            throw new InvalidGrantTypeException;
        }

        if (!$this->clientStorage->canUseGrantType($this->getClient()->getId(), $grantType)) {
            throw new UnauthorizedClientException;
        }
    }

    /****************** Access token template methods ******************/

    /**
     * Verify request
     * @return void
     */
    protected abstract function verifyRequest(): void;

    /**
     * Generate access token
     * @return array<string, string|int>
     */
    protected abstract function generateAccessToken(): array;

    /**
     * Get scope as array - allowed separators: ',' AND ' '
     * @return array
     */
    protected function getScope(): array
    {
        $scope = $this->input->getParameter(self::SCOPE_KEY);
        return !is_array($scope) ?
            array_filter(explode(',', str_replace(' ', ',', strval($scope)))) :
            $scope;
    }
}
