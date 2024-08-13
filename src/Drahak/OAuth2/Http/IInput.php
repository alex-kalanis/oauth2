<?php

namespace Drahak\OAuth2\Http;

/**
 * Request input data interface
 * @package Drahak\OAuth2\Http
 * @author Drahomír Hanák
 */
interface IInput
{

    /**
     * Get all parameters
     * @return array
     */
    public function getParameters();

    /**
     * Get single parameter value by name
     * @param string $name
     * @return string|int
     */
    public function getParameter($name);

    /**
     * Get authorization token
     * @return string
     */
    public function getAuthorization();

}