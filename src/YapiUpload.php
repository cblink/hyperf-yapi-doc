<?php

namespace Cblink\Hyperf\Yapi;

use Cblink\YApi\YApiRequest;
use Hyperf\Utils\Arr;

class YapiUpload {

    protected $config;

    protected $basePath;

    public function __construct($config, $basePath)
    {
        $this->config = $config;
        $this->basePath = $basePath;
    }

    public function upload()
    {
        $yapi = new YApiRequest(Arr::get($this->config, 'base_url'));

        foreach (scandir($this->basePath) as $path) {

            if (in_array($path, ['.', '..'])) continue;

            if (!Arr::has($this->config, sprintf('config.%s', $path))) continue;

            $swagger = $this->loadSwagger($path);

            if (empty($swagger['paths'])) continue;

            $yapi->setConfig(
                Arr::get($this->config, sprintf('config.%s.id', $path), ''),
                Arr::get($this->config, sprintf('config.%s.token', $path), '')
            )
                ->importData(
                    json_encode($swagger, JSON_UNESCAPED_UNICODE),
                    Arr::get($this->config, 'merge', 'normal')
                );
        }
    }

    /**
     * @param $project
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function loadSwagger($project)
    {
        $swagger = ["swagger" => "2.0", "paths" => []];

        $update = [];

        foreach (scandir($this->basePath .$project) as $file) {
            if (in_array($file, ['.', '..'])) continue;

            list($method, $uri, $content) = $this->getSwaggerByFile($project, $file);

            array_push($update, [
                'method' => $method,
                'url' => $uri,
            ]);

            $swagger['paths'][$uri][$method] = $content;
        }

        return $swagger;
    }

    /**
     * 获取swagger信息
     *
     * @param $project
     * @param $file
     * @return array
     */
    public function getSwaggerByFile($project, $file)
    {
        $example = explode("@", substr($file, 0, -5));

        $method = strtolower($example[0]);

        unset($example[0]);

        $content = json_decode(file_get_contents(sprintf("%s%s/%s", $this->basePath, $project, $file)), true);

        return [$method, implode('/', $example), $content];
    }

}