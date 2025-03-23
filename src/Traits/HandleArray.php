<?php

namespace Cblink\Hyperf\Yapi\Traits;

use Hyperf\Collection\Arr;

trait HandleArray
{
    /**
     * @param $payload
     * @return string
     */
    protected function getType($payload)
    {
        return array_keys($payload) === range(0, count($payload) - 1) ?
            'array' :
            'object';
    }

    /**
     * 格式化返回类型
     *
     * @param $val
     * @return string
     */
    public function formatReturnType($val): string
    {
        if (is_int($val)) {
            return 'integer';
        }

        if (is_float($val)) {
            return 'number';
        }

        if (is_bool($val)) {
            return 'boolean';
        }

        return 'string';
    }

    /**
     * 将array数据转换成yapi文档格式
     *
     * @param array $payload
     * @param array $prefix
     * @param string $var
     * @return array
     */
    protected function handlerArray(array $payload = [], array $prefix = [], string $var = 'request')
    {
        // 获取payload类型，对象或数组
        $type = $this->getType($payload);

        // 获取前缀
        $prefixString = implode('.', $prefix);

        $desc = Arr::get($this->getItem($var), $prefixString, '');

        $data = [
            'type' => $type,
            'description' => $desc,
            ($type == 'array' ? 'items' : 'properties') => [],
        ];

        if ($type === 'array') {
            // 数组返回只需要处理子集的数据
            foreach ($payload as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $data['items'] = $this->handlerArray(
                    $item,
                    array_merge($prefix, ['*']),
                    $var
                );
                break;
            }
        } else {
            // 必填字段
            $data['required'] = array_keys(Arr::except($payload, $this->getResult(sprintf('%s_except', $var), $prefixString)));

            foreach ($payload as $key => $item) {

                // 当前key的前缀
                $currentString = implode('.', array_merge($prefix, [$key]));

                // 当前备注
                $currentRemark = Arr::get($this->getItem($var), $currentString);

                if (is_array($item)) {
                    $data['properties'][$key] = $this->handlerArray($item, array_merge($prefix, [$key]), $var);
                    continue;
                }

                $data['properties'][$key] = [
                    'type' => $this->formatReturnType($item),
                    'description' => $currentRemark
                ];
            }
        }

        return $data;
    }
}
