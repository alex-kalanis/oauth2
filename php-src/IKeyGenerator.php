<?php

namespace kalanis\OAuth2;


/**
 * IKeyGenerator
 * @package kalanis\OAuth2
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
