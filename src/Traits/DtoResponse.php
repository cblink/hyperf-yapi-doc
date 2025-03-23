<?php

namespace Cblink\Hyperf\Yapi\Traits;

use Cblink\Hyperf\Yapi\TestResponse;
use Hyperf\Collection\Arr;

trait DtoResponse
{
    public function getResponse(TestResponse $response)
    {
        $response = $response->response();

        if (!is_array($response)) {
            return [
                "200" => [
                    "description" => "successful operation",
                    "schema" => [
                        "type" => "string",
                        "format" => "binary",
                        "default" => $this->getItem('response_raw', '')
                    ]
                ]
            ];
        }


        $publicData = $this->getOptions(Arr::get($this->getConfig(), 'public.data', []), $response);

        $schema = [
            "type" =>  "object",
            "title" =>  "empty object",
        ];

        $schema = array_merge($publicData, $schema);

        $payload = $this->getPayload($response);

        // 处理子集
        $data = $this->handlerArray((is_array($payload) ? $payload : []), [], 'response');

        if (!empty($data)) {
            $schema['properties'][Arr::get($this->getConfig(), 'public.prefix')] = $data;
        }

        return [
            "200" =>  [
                "description" =>  "successful operation",
                "schema" => $schema
            ]
        ];
    }

    /**
     * @param $options
     * @param $response
     * @return array[]
     */
    protected function getOptions($options, $response)
    {
        $item = ['properties' => [], 'required' => []];

        foreach ($options as $key => $val) {
            $must = $val['must'] ?? false;
            $type = $val['type'] ?? 'string';

            if (!$must && !Arr::has($response, $key)) {
                continue;
            }

            if (array_key_exists('required', $val) && $val['required']) {
                array_push($item['required'], $key);
            }

            $item['properties'][$key] = [
                'type' => isset($val['children']) ? 'object' : (isset($response[$key]) ? $this->formatReturnType($response[$key]) : $type),
                'description' => $val['plan'] ?? '',
            ];

            if (!empty($val['children'])) {
                $item['properties'][$key] = array_merge($item['properties'][$key], $this->getOptions($val['children'], $response[$key] ?? []));
            }
        }

        return $item;
    }

    /**
     * @param $response
     * @return mixed
     */
    public function getPayload($response)
    {
        $prefix = Arr::get($this->getConfig(), 'public.prefix');

        if (!empty($prefix) && isset($response[$prefix])) {
            return $response[$prefix];
        }

        return $response;
    }
}
