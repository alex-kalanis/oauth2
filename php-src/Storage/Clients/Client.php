<?php

namespace kalanis\OAuth2\Storage\Clients;


/**
 * OAuth2 base client caret
 * @package kalanis\OAuth2\Storage\Clients
 */
class Client implements IClient
{

    public function __construct(
        private readonly string|int $id,
        #[\SensitiveParameter] private readonly string $secret,
        private readonly string $redirectUrl,
    )
    {
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }
}
