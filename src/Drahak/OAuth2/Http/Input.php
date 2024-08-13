<?php

namespace Drahak\OAuth2\Http;

use Nette\Http\IRequest;
use Nette\SmartObject;

/**
 * Input parser
 * @package Drahak\OAuth2\Http
 * @author Drahomír Hanák
 */
class Input implements IInput
{
    use SmartObject;

    /** @var array|null */
    private ?array $data = null;

    public function __construct(
        private readonly IRequest $request
    )
    {
    }

    /**
     * Get single parameter by key
     * @param string $name
     * @return string|int|null
     */
    public function getParameter(string $name): string|int|null
    {
        $parameters = $this->getParameters();
        return $parameters[$name] ?? NULL;
    }

    /**
     * Get all parameters
     * @return array
     */
    public function getParameters(): array
    {
        if (is_null($this->data)) {
            if ($this->request->getQuery()) {
                $this->data = $this->request->getQuery();
            } else if ($this->request->getPost()) {
                $this->data = $this->request->getPost();
            } else {
                $this->data = $this->parseRequest(file_get_contents('php://input'));
            }
        }
        return $this->data;
    }

    /**
     * Convert client request data to array or traversable
     * @param string $data
     * @return array
     */
    private function parseRequest(string $data): array
    {
        $result = [];
        parse_str($data, $result);
        return $result;
    }

    /**
     * Get authorization token from header - Authorization: Bearer
     * @return string|null
     */
    public function getAuthorization(): ?string
    {
        $authorization = explode(' ', (string) $this->request->getHeader('Authorization'));
        return $authorization[1] ?? NULL;
    }
}
