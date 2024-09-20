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
     * @return array<string, mixed>
     */
    public function getParameters(): array;

    /**
     * Get single parameter value by name
     * @param string $name
     * @return mixed
     */
    public function getParameter(string $name): mixed;

    /**
     * Get authorization token
     * @return string|null
     */
    public function getAuthorization(): string|null;
}
