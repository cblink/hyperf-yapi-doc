<?php

namespace Cblink\Hyperf\Yapi\Traits;

use Cblink\Hyperf\Yapi\Yapi;

trait DtoBody
{

    public function getBody(Yapi $yapi)
    {
        if (!in_array($yapi->method(), ['POST', 'PUT'])) {
            return [];
        }

        return $yapi->request() ? [[
            "name" => "root",
            "in" => "body",
            "schema" =>  array_merge([
                "type" => "object",
                "title" => "empty object",
            ], $this->handlerArray($yapi->request(), []))
        ]] : [];
    }
}
