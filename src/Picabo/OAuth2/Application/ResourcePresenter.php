<?php

namespace Picabo\OAuth2\Application;

use Picabo\OAuth2\Http\IInput;
use Picabo\OAuth2\Storage\AccessTokens\AccessToken;
use Picabo\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use Picabo\OAuth2\Storage\Exceptions\InvalidAccessTokenException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Presenter;

/**
 * OAuth2 secured resource presenter
 * @package Picabo\OAuth2\Application
 * @author Drahomír Hanák
 */
abstract class ResourcePresenter extends Presenter implements IResourcePresenter
{

    protected AccessTokenFacade $accessToken;
    private IInput $input;

    /**
     * Standard input parser
     */
    public function injectInput(IInput $input): void
    {
        $this->input = $input;
    }

    /**
     * Access token manager facade
     * @param AccessTokenFacade $accessToken
     */
    public function injectAccessToken(AccessTokenFacade $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Check presenter requirements
     * @param \ReflectionClass<object>|\ReflectionMethod $element
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
