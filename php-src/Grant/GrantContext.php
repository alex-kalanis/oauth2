<?php

namespace kalanis\OAuth2\Grant;


use kalanis\OAuth2\Exceptions\InvalidStateException;


/**
 * GrantContext
 * @package kalanis\OAuth2\Grant
 */
class GrantContext
{

    /** @var array<IGrant> */
    private array $grantTypes = [];

    /**
     * Add grant type
     */
    public function addGrantType(IGrant $grantType): void
    {
        $this->grantTypes[$grantType->getIdentifier()] = $grantType;
    }

    /**
     * Remove grant type from strategy context
     * @param string $grantType
     */
    public function removeGrantType(string $grantType): void
    {
        unset($this->grantTypes[$grantType]);
    }

    /**
     * Get grant type
     * @param string $grantType
     * @throws InvalidStateException
     * @return IGrant
     */
    public function getGrantType(string $grantType): IGrant
    {
        if (!isset($this->grantTypes[$grantType])) {
            throw new InvalidStateException('Grant type ' . $grantType . ' is not registered in GrantContext');
        }
        return $this->grantTypes[$grantType];
    }
}
