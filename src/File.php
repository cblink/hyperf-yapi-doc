<?php

namespace Cblink\Hyperf\Yapi;

class File
{

    /**
     * @param $key
     * @param array $content
     * @return false|int
     */
    public static function put($key, array $content = [])
    {
        $basePath = BASE_PATH .'/runtime/yapi/';

        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }

        $file = $basePath.$key;

        if (!file_exists(dirname($file))){
            mkdir(dirname($file), 0777, true);
        }

        return file_put_contents($basePath.$key, json_encode($content));
    }

}
