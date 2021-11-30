<?php

namespace Cblink\Hyperf\Yapi\Traits;

use Cblink\Hyperf\Yapi\TestResponse;

trait DtoBody
{

    public function getBody(TestResponse $response)
    {
        if (!in_array($response->method(), ['POST', 'PUT'])) {
            return [];
        }

        return $response->request() ? [[
            "name" => "root",
            "in" => "body",
            "schema" =>  array_merge([
                "type" => "object",
                "title" => "empty object",
            ], $this->handlerArray($response->request(), []))
        ]] : [];
    }
}
