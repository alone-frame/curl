<?php

namespace AloneFrame\curl;

use AloneFrame\curl\process\Method;
use AloneFrame\curl\process\BodyCall;
use AloneFrame\curl\process\BodySend;

class CurlRequest {
    /**
     * 设置全局代理ip
     * @param array $config
     * @return void
     */
    public static function proxy(array $config): void {
        Method::$proxy['config'] = array_merge(Method::$proxy['config'] ?? [], $config['config'] ?? []);
        Method::$proxy['default'] = ($config['default'] ?? Method::$proxy['config'] ?? '') ?: key(Method::$proxy['config']);
    }

    /**
     * 批请求
     * @param array|string $config
     * @return BodySend
     */
    public static function send(array|string $config): BodySend {
        $curl = [];
        $init = curl_multi_init();
        $config = is_array($config) ? $config : ['url' => $config];
        $config = isset($config['url']) ? [$config] : $config;
        foreach ($config as $key => $val) {
            $value = Method::setCurl($val);
            $curl[$key]['status'] = true;
            $curl[$key]['time'] = microtime(true);
            $curl[$key]['request'] = $value['request'] ?? [];
            $curl[$key]['conn'] = curl_init($value['url']);
            foreach ($value['curl'] as $v) {
                $keys = key($v);
                $curl[$key]['curl'][$keys] = $v[$keys];
            }
            curl_setopt_array($curl[$key]['conn'], $curl[$key]['curl']);
            curl_multi_add_handle($init, $curl[$key]['conn']);
        }
        do {
            $exec = curl_multi_exec($init, $active);
            if ($active) {
                curl_multi_select($init, 10);
            }
        } while ($active && $exec == CURLM_OK);
        $res = [];
        foreach ($curl as $key => $v) {
            $res[$key]['status'] = true;
            $res[$key]['request'] = $v['request'];
            $res[$key]['info'] = curl_getinfo($v['conn']);
            $res[$key]['code'] = $res[$key]['info']['http_code'] ?? 0;
            $size = $res[$key]['info']['header_size'] ?? 0;
            $response = curl_multi_getcontent($v['conn']);
            $res[$key]['header'] = trim(trim(substr($response, 0, $size), "\r\n"), "\r\n");
            if (curl_errno($v['conn'])) {
                $res['status'] = false;
                $res['body'] = curl_error($v['conn']);
            } else {
                $res[$key]['body'] = substr($response, $size);
            }
            curl_multi_remove_handle($init, $v['conn']);
            curl_close($v['conn']);
            $res[$key]['time'] = microtime(true) - $v['time'];
        }
        return new BodySend($res);
    }

    /**
     * 单请求
     * @param array|string $config
     * @return BodyCall
     */
    public static function call(array|string $config): BodyCall {
        $config = is_array($config) ? $config : ['url' => $config];
        $value = Method::setCurl(isset($config['url']) ? $config : $config[key($config)]);
        $time = microtime(true);
        $res['status'] = true;
        $res['request'] = $value['request'] ?? [];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $value['url']);
        foreach ($value['curl'] as $v) {
            $key = key($v);
            curl_setopt($curl, $key, $v[$key]);
        }
        $response = curl_exec($curl);
        $res['info'] = curl_getinfo($curl);
        $res['code'] = $res['info']['http_code'] ?? 0;
        $size = $res['info']['header_size'] ?? 0;
        $res['header'] = trim(trim(substr($response, 0, $size), "\r\n"), "\r\n");
        if (curl_errno($curl)) {
            $res['status'] = false;
            $res['body'] = curl_error($curl);
        } else {
            $res['body'] = substr($response, $size);
        }
        $res['time'] = microtime(true) - $time;
        curl_close($curl);
        return new BodyCall($res);
    }
}