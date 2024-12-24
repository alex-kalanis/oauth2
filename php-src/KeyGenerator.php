<?php

namespace kalanis\OAuth2;


use Nette\SmartObject;


/**
 * KeyGenerator
 * @package kalanis\OAuth2
 */
class KeyGenerator implements IKeyGenerator
{
    use SmartObject;

    /** Key generator algorithm */
    public const ALGORITHM = 'sha256';

    /**
     * Generate random token
     * @param int $length
     */
    public function generate(int $length = 40): string
    {
        $bytes = openssl_random_pseudo_bytes($length);
        return hash(self::ALGORITHM, $bytes);
    }
}
