<?php

namespace Picabo\OAuth2\Storage;

use Picabo\OAuth2\Exceptions\InvalidStateException;
use Nette\SmartObject;

/**
 * TokenContext
 * @package Picabo\OAuth2\Token
 * @author Drahomír Hanák
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
     * @return ITokenFacade
     *
     * @throws InvalidStateException
     */
    public function getToken(string $identifier): ITokenFacade
    {
        if (!isset($this->tokens[$identifier])) {
            throw new InvalidStateException('Token called "' . $identifier . '" not found in Token context');
        }

        return $this->tokens[$identifier];
    }
}
