<?php

namespace Cblink\Hyperf\Yapi\Traits;

trait DtoParams
{
    /**
     * 获取路由参数
     *
     * @return array
     */
    public function getParams(): array
    {
        $items = [];

        $params = $this->getItem('params');

        if ($params) {
            foreach ($params as $num => $desc) {

                $required = strstr('?', (string) $num);
                $num = str_replace( '?', '', (string) $num);

                $items[] = [
                    "name" => $num,
                    "in" => "path",
                    "description" => $desc,
                    "required" => !!$required,
                    "type" => "string"
                ];
            }
        }

        return $items;
    }
}
