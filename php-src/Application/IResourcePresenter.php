<?php

namespace kalanis\OAuth2\Application;


use Nette\Application\IPresenter;


/**
 * OAuth2 resource server presenter
 * @package kalanis\OAuth2\Application
 */
interface IResourcePresenter extends IPresenter
{

    /**
     * Check if access token is valid
     * @param string $accessToken
     * @return void
     */
    public function checkAccessToken(string $accessToken): void;
}
