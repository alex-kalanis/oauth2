<?php

namespace Drahak\OAuth2\Grant;

use Drahak\OAuth2\Exceptions\InvalidStateException;
use Nette\SmartObject;

/**
 * GrantContext
 * @package Drahak\OAuth2\Grant
 * @author Drahomír Hanák
 */
class GrantContext
{
    use SmartObject;

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
     * @return IGrant
     *
     * @throws InvalidStateException
     */
    public function getGrantType(string $grantType): IGrant
    {
        if (!isset($this->grantTypes[$grantType])) {
            throw new InvalidStateException('Grant type ' . $grantType . ' is not registered in GrantContext');
        }
        return $this->grantTypes[$grantType];
    }
}
