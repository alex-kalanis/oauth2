<?php

namespace kalanis\OAuth2\Storage;


use kalanis\OAuth2\Exceptions\InvalidStateException;
use Nette\SmartObject;


/**
 * TokenContext
 * @package kalanis\OAuth2\Storage
 */
class TokenContext
{
    use SmartObject;

    /** @var array<ITokenFacade> */
    private array $tokens = [];

    /**
     * Add identifier to collection
     */
    public function addToken(ITokenFacade $token): void
    {
        $this->tokens[$token->getIdentifier()] = $token;
    }

    /**
     * Get token
     * @param string $identifier
     * @throws InvalidStateException
     * @return ITokenFacade
     */
    public function getToken(string $identifier): ITokenFacade
    {
        if (!isset($this->tokens[$identifier])) {
            throw new InvalidStateException('Token called "' . $identifier . '" not found in Token context');
        }

        return $this->tokens[$identifier];
    }
}
