<?php

namespace Tests\Picabo\OAuth2\Http;

require_once __DIR__ . '/../../bootstrap.php';

use Picabo\OAuth2\Http\Input;
use Mockery;
use Tester\Assert;
use Tests\TestCase;

class InputTest extends TestCase
{

    private $request;

    private Input $input;

    public function testGetParameterByName(): void
    {
        $this->request->expects('getQuery')->once()->andReturn(null);
        $this->request->expects('getPost')->once()->andReturn(['test' => 'hello']);
        $value = $this->input->getParameter('test');

        Assert::equal($value, 'hello');
    }

    public function testGetAllParameters(): void
    {
        $input = ['test' => 'hello', 'oauth2' => TRUE];
        $this->request->expects('getQuery')->once()->andReturn(null);
        $this->request->expects('getPost')->once()->andReturn($input);
        $params = $this->input->getParameters();
        Assert::equal($input, $params);
    }

    public function testGetAccessToken(): void
    {
        $token = '546ad8fs8bd18be8tj48ku8yl418uo4vs81515';
        $this->request->expects('getHeader')
            ->once()
            ->with('Authorization')
            ->andReturn('Bearer ' . $token);

        $accessToken = $this->input->getAuthorization();
        Assert::equal($accessToken, $token);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = Mockery::mock(\Nette\Http\IRequest::class);
        $this->input = new Input($this->request);
    }
}

(new InputTest())->run();
