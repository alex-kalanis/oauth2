<?php

namespace kalanis\OAuth2\Application;


use kalanis\OAuth2\Http\IInput;
use kalanis\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use kalanis\OAuth2\Storage\Exceptions\InvalidAccessTokenException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Presenter;


/**
 * OAuth2 secured resource presenter
 * @package kalanis\OAuth2\Application
 */
abstract class ResourcePresenter extends Presenter implements IResourcePresenter
{

    /** Access token manager facade */
    #[\Nette\DI\Attributes\Inject]
    public AccessTokenFacade $accessToken;

    /** Standard input parser */
    #[\Nette\DI\Attributes\Inject]
    public IInput $input;

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
     * @throws ForbiddenRequestException
     * @return void
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
