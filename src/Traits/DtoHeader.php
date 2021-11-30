<?php

namespace Cblink\Hyperf\Yapi\Traits;

use Cblink\Hyperf\Yapi\TestResponse;
use Hyperf\Utils\Arr;

trait DtoHeader
{

    public function getHeaders(TestResponse $response): array
    {
        $items = [];

        foreach ($response->headers() as $key => $value) {
            $description = sprintf(
                "%s %s",
                (is_array($value) ? 'array :': ''),
                $this->getResult('headers', $key)
            );

            if (Arr::has($this->getConfig(), sprintf('public.headers.%s', $key))) {

                $plan = Arr::get($this->getConfig(), sprintf('public.headers.%s', $key));

                $item = [
                    'name' => $key,
                    'required' => true,
                    'description' => empty(trim($description)) && !empty($plan) ? $plan['plan'] : $description,
                    'in' => 'header',
                    'type' => 'string',
                ];

            } else {
                $item = [
                    'name' => $key,
                    'required' => Arr::has($this->payload, sprintf('headers_except.%s', $key)),
                    'description' => $description,
                    'in' => 'query',
                    'type' => 'string',
                ];
            }

            array_push($items, $item);
        }

        return $items;
    }

}
