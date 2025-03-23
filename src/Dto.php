<?php

namespace Cblink\Hyperf\Yapi;

use Hyperf\Collection\Arr;
use Cblink\Dto\Dto as BaseDto;
use Cblink\Hyperf\Yapi\Traits\DtoBody;
use Cblink\Hyperf\Yapi\Traits\DtoHeader;
use Cblink\Hyperf\Yapi\Traits\DtoParams;
use Cblink\Hyperf\Yapi\Traits\DtoQuery;
use Cblink\Hyperf\Yapi\Traits\DtoResponse;
use Cblink\Hyperf\Yapi\Traits\HandleArray;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

/**
 * @property-read $project
 * @property-read $name
 * @property-read $desc
 * @property-read $category
 * @property-read array $params
 * @property-read array $request
 * @property-read array $response
 * @property-read array $request_except
 * @property-read array $response_except
 */
abstract class Dto extends BaseDto
{
    use HandleArray, DtoParams, DtoQuery, DtoBody, DtoResponse, DtoHeader;

    protected $fillable = [
        'project',
        'name',
        'category',
        'desc',

        'params',

        'headers',
        'headers_except',

        'request',
        'request_except',

        'response',
        'response_except'
    ];

    /**
     * 模型常量转换成注释
     *
     * @param $name
     * @param array $array
     * @param bool $notKeys
     * @return string|string[]
     */
    public static function mapDesc($name, array $array = [], bool $notKeys = false)
    {
        if ($notKeys) {
            $return = implode(" \n ", $array);
        } else {
            $return = urldecode(http_build_query($array));
            $return = str_replace('=', ' : ', $return);
            $return = str_replace('&', " \n ", $return);
        }

        return sprintf('%s %s%s', $name, "\n ", $return);
    }

    public function validate()
    {
        make(ValidatorFactoryInterface::class)->make($this->payload, [
            'project' => ['nullable', 'array'],
            'name' => ['required', 'string'],
            'category' => ['required', 'string'],
            'desc' => ['nullable', 'string'],

            'params' => ['nullable', 'array'],
            'params.*' => ['nullable', 'string'],

            'headers' => ['nullable', 'array'],

            'request' => ['nullable', 'array'],
            'request_except' => ['nullable', 'array'],

            'response' => ['nullable', 'array'],
            'response_except' => ['nullable', 'array'],
        ])->validate();
    }

    /**getRequestTrans
     * @param $name
     * @param $key
     * @param null $default
     * @return array|\ArrayAccess|mixed
     */
    protected function getResult($name, $key, $default = '')
    {
        $payload = Arr::get($this->payload, $name);

        if (empty($key)) {
            return $payload;
        }

        if (substr($key, 0, 1) == '.') {
            $key = substr($key, 1);
        }

        return Arr::get($payload, $key, $default);
    }

    /**
     * @return array|\ArrayAccess|mixed|string
     */
    public function getUrl($url)
    {
        if ($this->getItem('params')) {

            $urls = array_values(explode('/', $url));

            foreach ($this->getItem('params') as $key => $value) {

                if (!is_int($key) || !isset($urls[$key])) {
                    continue;
                }
                $urls[$key] = sprintf('{%s}', $key);
            }

            $url = implode('/', $urls);
        }

        return $url;
    }

    /**
     * @param TestResponse $response
     */
    public function builder(TestResponse $response)
    {
        $content = [
            "tags" =>  [$this->category],
            "summary" =>  $this->name,
            "description" => $this->desc ?? '',
            "consumes" => [
                "application/json"
            ],
            "parameters" => array_merge(
                $this->getParams(),
                $this->getHeaders($response),
                $this->getQuery($response),
                $this->getBody($response)
            ),
            "responses" => $this->getResponse($response)
        ];

        foreach ($this->project ?? ['default'] as $project) {
            $file = strtolower($response->method() . str_replace( '/', '@', $this->getUrl($response->url())) . '.json');

            File::put(Arr::get($this->getConfig(), 'base_path'), $project . '/' . $file, $content);
        }
    }

    /**
     * @return mixed
     */
    abstract public function getConfig() :array;

}
