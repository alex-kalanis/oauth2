<?php

namespace Drahak\OAuth2;

/**
 * IKeyGenerator
 * @package Drahak\OAuth2
 * @author Drahomír Hanák
 */
interface IKeyGenerator
{

    /**
     * Generate random token
     * @param int $length
     * @return string
     */
    public function generate($length = 40);

}