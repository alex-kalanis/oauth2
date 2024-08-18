<?php

namespace Picabo\OAuth2;

/**
 * IKeyGenerator
 * @package Picabo\OAuth2
 * @author Drahomír Hanák
 */
interface IKeyGenerator
{

    /**
     * Generate random token
     * @param int $length
     * @return string
     */
    public function generate(int $length = 40): string;
}
