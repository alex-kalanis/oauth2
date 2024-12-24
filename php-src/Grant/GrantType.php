<?php

namespace kalanis\OAuth2\Grant;


use kalanis\OAuth2\Exceptions\UnauthorizedClientException;
use kalanis\OAuth2\Grant\Exceptions\InvalidGrantTypeException;
use kalanis\OAuth2\Http\IInput;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\Clients\IClientStorage;
use kalanis\OAuth2\Storage\TokenContext;
use Nette\Security\User;
use Nette\SmartObject;


/**
 * GrantType
 * @package kalanis\OAuth2\Grant
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
     * @return array<string, string|int>
     */
    public final function getAccessToken(): array
    {
        if (!$client = $this->getClient()) {
            throw new UnauthorizedClientException('Client is not found');
        }

        $this->verifyGrantType($client);
        $this->verifyRequest();
        return $this->generateAccessToken($client);
    }

    /**
     * Get client
     * @return IClient|null
     */
    private function getClient(): ?IClient
    {
        if (!$this->client) {
            $clientId = $this->input->getParameter(self::CLIENT_ID_KEY);
            $clientSecret = $this->input->getParameter(self::CLIENT_SECRET_KEY);
            $this->client = $this->clientStorage->getClient(
                is_numeric($clientId) ? intval($clientId) : strval($clientId),
                is_null($clientSecret) ? null : strval($clientSecret)
            );
        }
        return $this->client;
    }

    /****************** IGrant interface ******************/

    /**
     * Verify grant type
     * @param IClient $client
     * @throws UnauthorizedClientException
     * @throws InvalidGrantTypeException
     */
    protected function verifyGrantType(IClient $client): void
    {
        $grantType = $this->input->getParameter(self::GRANT_TYPE_KEY);
        if (!$grantType) {
            throw new InvalidGrantTypeException;
        }

        if (!$this->clientStorage->canUseGrantType($client->getId(), strval($grantType))) {
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
     * @param IClient $client
     * @return array<string, string|int>
     */
    protected abstract function generateAccessToken(IClient $client): array;

    /**
     * Get scope as array - allowed separators: ',' AND ' '
     * @return array<string>
     */
    protected function getScope(): array
    {
        $scope = $this->input->getParameter(self::SCOPE_KEY);
        return !is_array($scope) ?
            array_filter(explode(',', str_replace(' ', ',', strval($scope)))) :
            array_filter(array_map('strval', $scope));
    }
}
