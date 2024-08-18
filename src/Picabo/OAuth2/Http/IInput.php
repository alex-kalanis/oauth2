<?php

namespace Picabo\OAuth2\Http;

/**
 * Request input data interface
 * @package Picabo\OAuth2\Http
 * @author Drahomír Hanák
 */
interface IInput
{

    /**
     * Get all parameters
     * @return array
     */
    public function getParameters(): array;

    /**
     * Get single parameter value by name
     * @param string $name
     * @return string|int|null
     */
    public function getParameter(string $name): string|int|null;

    /**
     * Get authorization token
     * @return string|null
     */
    public function getAuthorization(): string|null;
}
