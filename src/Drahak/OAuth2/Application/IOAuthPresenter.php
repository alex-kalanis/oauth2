<?php

namespace Drahak\OAuth2\Application;

use Nette\Application\IPresenter;

/**
 * OAuth2 authorization server presenter
 * @package Drahak\OAuth2\Application
 * @author Drahomír Hanák
 */
interface IOAuthPresenter extends IPresenter
{

    /**
     * Issue an authorization code
     * @param string $responseType
     * @param string $redirectUrl
     * @param string|null $scope
     * @return void
     */
    public function issueAuthorizationCode(string $responseType, string $redirectUrl, ?string $scope = NULL): void;

    /**
     * Issue an access token
     * @return void
     */
    public function issueAccessToken(): void;
}
