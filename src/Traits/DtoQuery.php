<?php

namespace Cblink\Hyperf\Yapi\Traits;

use Cblink\Hyperf\Yapi\TestResponse;
use Hyperf\Utils\Arr;

trait DtoQuery
{
    /**
     * @param TestResponse $response
     * @return array
     */
    public function getQuery(TestResponse $response)
    {
        $items = [];

        if (in_array($response->method(), ['GET','DELETE'])) {

            foreach ($response->request() as $key => $value) {
                $description = sprintf(
                    "%s %s",
                    (is_array($value) ? 'array :': ''),
                    $this->getResult('request', $key)
                );

                if (Arr::has($this->getConfig(), sprintf('public.query.%s', $key))) {

                    $plan = Arr::get($this->getConfig(), sprintf('public.query.%s', $key));

                    $item = [
                        'name' => $key,
                        'required' => (bool) ($plan['required'] ?? Arr::has($this->payload, sprintf('request_except.%s', $key))),
                        'description' => empty(trim($description)) && !empty($plan) ? $plan['plan'] : $description,
                        'in' => 'query',
                        'type' => 'string',
                    ];

                } else {
                    $item = [
                        'name' => $key,
                        'required' => Arr::has($this->payload, sprintf('request_except.%s', $key)),
                        'description' => $description,
                        'in' => 'query',
                        'type' => 'string'
                    ];
                }

                array_push($items, $item);
            }

        }

        return $items;
    }
}
