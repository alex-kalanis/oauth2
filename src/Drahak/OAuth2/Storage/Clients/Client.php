<?php

namespace Drahak\OAuth2\Storage\Clients;

use Nette\SmartObject;

/**
 * OAuth2 base client caret
 * @package Drahak\OAuth2\Storage\Entity
 * @author Drahomír Hanák
 */
class Client implements IClient
{
    use SmartObject;

    public function __construct(
        private readonly string|int $id,
        #[\SensitiveParameter] private readonly string $secret,
        private readonly string $redirectUrl
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
