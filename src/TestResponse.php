<?php

namespace Cblink\Hyperf\Yapi;

class TestResponse
{
    protected string $method;

    protected string $url;

    protected array $request;

    protected array $headers;

    protected $response;

    public function __construct($method, string $url, array $request = [], array $headers = [], $response = [])
    {
        $this->method = $method;
        $this->url = $url;
        $this->request = $request;
        $this->headers = $headers;
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function request(): array
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    public function dump()
    {
        var_dump($this->response());
    }
}
