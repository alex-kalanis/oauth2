<?php

namespace kalanis\OAuth2\Http;


use Nette\Http\IRequest;


/**
 * Input parser
 * @package kalanis\OAuth2\Http
 */
class Input implements IInput
{

    /** @var array<string|int, mixed>|null */
    private ?array $data = null;

    public function __construct(
        private readonly IRequest $request,
    )
    {
    }

    /**
     * Get single parameter by key
     * @param string $name
     * @return mixed
     */
    public function getParameter(string $name): mixed
    {
        $parameters = $this->getParameters();
        return $parameters[$name] ?? null;
    }

    /**
     * Get all parameters
     * @return array<string|int, mixed>
     */
    public function getParameters(): array
    {
        if (is_null($this->data)) {
            if ($this->request->getQuery()) {
                $this->data = $this->request->getQuery();
            } else if ($this->request->getPost()) {
                $this->data = $this->request->getPost();
            } else {
                $this->data = $this->parseRequest(strval(file_get_contents('php://input')));
            }
        }
        return $this->data;
    }

    /**
     * Convert client request data to array or traversable
     * @param string $data
     * @return array<string|int, mixed>
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
        return $authorization[1] ?? null;
    }
}
