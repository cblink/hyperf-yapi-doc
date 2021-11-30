<?php

namespace Cblink\Hyperf\Yapi;

class File
{

    /**
     * @param $basePath
     * @param $key
     * @param array $content
     * @return false|int
     */
    public static function put($basePath, $key, array $content = [])
    {
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
