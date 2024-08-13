<?php

namespace Drahak\OAuth2\Application;

use Drahak\OAuth2\Grant\GrantContext;
use Drahak\OAuth2\Grant\GrantType;
use Drahak\OAuth2\Grant\IGrant;
use Drahak\OAuth2\Exceptions\InvalidGrantException;
use Drahak\OAuth2\Exceptions\InvalidStateException;
use Drahak\OAuth2\Exceptions\OAuthException;
use Drahak\OAuth2\Storage\AuthorizationCodes\AuthorizationCodeFacade;
use Drahak\OAuth2\Storage\Clients\IClient;
use Drahak\OAuth2\Storage\Clients\IClientStorage;
use Drahak\OAuth2\Storage\Exceptions\InvalidAuthorizationCodeException;
use Drahak\OAuth2\Storage\Exceptions\TokenException;
use Drahak\OAuth2\Exceptions\UnauthorizedClientException;
use Drahak\OAuth2\Exceptions\UnsupportedResponseTypeException;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\Http\Url;
use Traversable;

/**
 * OauthPresenter
 * @package Drahak\OAuth2\Application
 * @author Drahomír Hanák
 *
 * @property-read IGrant $grantType
 */
class OAuthPresenter extends Presenter implements IOAuthPresenter
{
    protected AuthorizationCodeFacade $authorizationCode;
    protected IClientStorage $clientStorage;
    protected IClient $client;
    private GrantContext $grantContext;

    /**
     * Inject grant strategy context
     */
    public function injectGrant(GrantContext $grantContext): void
    {
        $this->grantContext = $grantContext;
    }

    /**
     * Inject token manager - authorization code
     */
    public function injectAuthorizationCode(AuthorizationCodeFacade $authorizationCode): void
    {
        $this->authorizationCode = $authorizationCode;
    }

    /**
     * Injet client storage
     */
    public function injectClientStorage(IClientStorage $clientStorage): void
    {
        $this->clientStorage = $clientStorage;
    }

    /**
     * @param string $responseType
     * @param string $redirectUrl
     * @param string|null $scope
     */
    public function issueAuthorizationCode(string $responseType, string $redirectUrl, ?string $scope = NULL): void
    {
        try {
            if ($responseType !== 'code') {
                throw new UnsupportedResponseTypeException;
            }
            if (!$this->client->getId()) {
                throw new UnauthorizedClientException;
            }

            $scope = array_filter(explode(',', str_replace(' ', ',', $scope)));
            $code = $this->authorizationCode->create($this->client, $this->user->getId(), $scope);
            $data = ['code' => $code->getAuthorizationCode()];
            $this->oauthResponse($data, $redirectUrl);
        } catch (OAuthException $e) {
            $this->oauthError($e);
        } catch (TokenException) {
            $this->oauthError(new InvalidGrantException);
        }
    }

    /**
     * Send OAuth response
     * @param iterable $data
     * @param string|null $redirectUrl
     * @param int $code
     */
    #[NoReturn]
    public function oauthResponse(iterable $data, ?string $redirectUrl = NULL, int $code = 200): void
    {
        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        }
        $data = (array)$data;

        // Redirect, if there is URL
        if ($redirectUrl !== NULL) {
            $url = new Url($redirectUrl);
            if ($this->getParameter('response_type') == 'token') {
                $url->setFragment(http_build_query($data));
            } else {
                $url->appendQuery($data);
            }
            $this->redirectUrl($url);
        }

        // else send JSON response
        foreach ($data as $key => $value) {
            $this->payload->$key = $value;
        }
        $this->getHttpResponse()->setCode($code);
        $this->sendResponse(new JsonResponse($this->payload));
    }

    /**
     * Provide OAuth2 error response (redirect or at least JSON)
     */
    #[NoReturn]
    public function oauthError(OAuthException $exception): void
    {
        $error = ['error' => $exception->getKey(), 'error_description' => $exception->getMessage()];
        $this->oauthResponse($error, $this->getParameter('redirect_uri'), $exception->getCode());
    }

    /**
     * Issue access token to client
     * @param string|null $grantType
     * @param string|null $redirectUrl
     *
     * @throws InvalidAuthorizationCodeException
     * @throws InvalidStateException
     */
    public function issueAccessToken(?string $grantType = NULL, ?string $redirectUrl = NULL): void
    {
        try {
            if ($grantType !== NULL) {
                $grantType = $this->grantContext->getGrantType($grantType);
            } else {
                $grantType = $this->getGrantType();
            }

            $response = $grantType->getAccessToken($this->getHttpRequest());
            $this->oauthResponse($response, $redirectUrl);
        } catch (OAuthException $e) {
            $this->oauthError($e);
        } catch (TokenException) {
            $this->oauthError(new InvalidGrantException);
        }
    }

    /**
     * Get grant type
     * @return IGrant
     * @throws UnsupportedResponseTypeException
     */
    public function getGrantType(): IGrant
    {
        $request = $this->getHttpRequest();
        $grantType = $request->getPost(GrantType::GRANT_TYPE_KEY);
        try {
            return $this->grantContext->getGrantType($grantType);
        } catch (InvalidStateException $e) {
            throw new UnsupportedResponseTypeException('Trying to use unknown grant type ' . $grantType, $e);
        }
    }

    /**
     * On presenter startup
     */
    protected function startup(): void
    {
        parent::startup();
        $this->client = $this->clientStorage->getClient(
            $this->getParameter(GrantType::CLIENT_ID_KEY),
            $this->getParameter(GrantType::CLIENT_SECRET_KEY)
        );
    }
}
