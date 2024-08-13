<?php

namespace Drahak\OAuth2\Application;

use Drahak\OAuth2\Http\IInput;
use Drahak\OAuth2\Storage\AccessTokens\AccessToken;
use Drahak\OAuth2\Storage\Exceptions\InvalidAccessTokenException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Presenter;

/**
 * OAuth2 secured resource presenter
 * @package Drahak\OAuth2\Application
 * @author Drahomír Hanák
 */
abstract class ResourcePresenter extends Presenter implements IResourcePresenter
{

    protected AccessToken $accessToken;
    private IInput $input;

    /**
     * Standard input parser
     */
    public function injectInput(IInput $input): void
    {
        $this->input = $input;
    }

    /**
     * Access token manager
     * @param AccessToken $accessToken
     */
    public function injectAccessToken(AccessToken $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Check presenter requirements
     * @param \ReflectionClass|\ReflectionMethod $element
     * @throws ForbiddenRequestException
     */
    public function checkRequirements(\ReflectionClass|\ReflectionMethod $element): void
    {
        parent::checkRequirements($element);
        $accessToken = $this->input->getAuthorization();
        if (!$accessToken) {
            throw new ForbiddenRequestException('Access token not provided');
        }
        $this->checkAccessToken($accessToken);
    }

    /**
     * Check if access token is valid
     * @param string $accessToken
     * @return void
     * @throws ForbiddenRequestException
     */
    public function checkAccessToken(string $accessToken): void
    {
        try {
            $this->accessToken->getEntity($accessToken);
        } catch (InvalidAccessTokenException $e) {
            throw new ForbiddenRequestException('Invalid access token provided. Use refresh token to grant new one.', 0, $e);
        }
    }
}
