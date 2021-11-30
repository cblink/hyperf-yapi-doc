<?php

namespace Cblink\Hyperf\Yapi;

use Hyperf\HttpMessage\Server\Response;
use HyperfTest\Yapi\Dto as YapiDto;

class Yapi
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
     * @return array|Response
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

    /**
     * @param Dto $dto
     * @return bool
     */
    public function build(YapiDto $dto) :bool
    {
        $content = [
            "tags" =>  [$dto->category],
            "summary" =>  $dto->name,
            "description" => $dto->desc ?? '',
            "consumes" => [
                "application/json"
            ],
            "parameters" => array_merge(
                $dto->getParams(),
                $dto->getHeaders($this),
                $dto->getQuery($this),
                $dto->getBody($this)
            ),
            "responses" => $dto->getResponse($this)
        ];

        foreach ($dto->project as $project) {
            $file = strtolower($this->method() . str_replace( '/', '@', $dto->getUrl($this->url())) . '.json');

            File::put($project . '/' . $file, $content);
        }

        return true;
    }
}
