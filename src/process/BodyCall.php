<?php

namespace AloneFrame\curl\process;

class BodyCall {
    //响应状态
    public bool $status = false;

    //请求信息
    public array $request = [];

    //响应时间
    public int|float $time = 0;

    //curl_get_info()响应信息
    public array $info = [];

    //响应状态码
    public string|int $code = 0;

    //响应头部信息
    public string $header = '';

    //响应内容
    public string $body = '';

    public function __construct(array $res) {
        foreach ($res as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * 输出调试信息
     * @return array
     */
    public function debug(): array {
        return [
            'request'  => $this->request,
            'response' => [
                'time'   => $this->time,
                'header' => $this->header,
                'body'   => $this->body
            ]
        ];
    }

    /**
     * 获取body信息 array
     * @return array
     */
    public function array(): array {
        return Method::isJson($this->body);
    }

    /**
     * 获取头部信息 array
     * @param string|int|null $key
     * @param mixed           $def
     * @return mixed
     */
    public function header(string|int|null $key = null, mixed $def = ''): mixed {
        $headers = [];
        $header = explode("\r\n", trim($this->header));
        foreach ($header as $head) {
            if (str_contains($head, ':')) {
                [$k, $v] = explode(': ', $head, 2);
                $keys = str_replace('-', '_', strtolower(trim($k)));
                $headers[$keys] = Method::isJson($v) ?: $v;
            }
        }
        if (isset($key)) {
            $keys = str_replace('-', '_', strtolower(trim($key)));
            return $headers[$keys] ?? $def;
        }
        return $headers;
    }

    /**
     * 获取头部信息 array
     * _换成-,每个开头大写
     * @return array
     */
    public function headers(): array {
        $headers = $this->header();
        foreach ($headers as $key => $value) {
            $keys = str_replace('_', '-', $key);
            $keys = ucwords(str_replace('-', ' ', $keys));
            $keys = str_replace(' ', '-', $keys);
            $head[$keys] = $value;
        }
        return $head ?? [];
    }

    /**
     * 获取所有 Cookie 信息的二维数组
     * @return array
     */
    public function cookie(): array {
        preg_match_all('/^Set-Cookie:\s*([^\r\n]*)/mi', $this->header, $matches);
        foreach ($matches[1] as $cookie) {
            preg_match_all('/([^=;]+)=([^;]*);?\s*/', $cookie, $cookieAttributes);
            $path = '/';
            $expires = '';
            foreach ($cookieAttributes[1] as $index => $attrKey) {
                $attrValue = $cookieAttributes[2][$index];
                if (strtolower($attrKey) === 'path') {
                    $path = $attrValue;
                } elseif (strtolower($attrKey) === 'expires') {
                    $expires = $attrValue;
                }
            }
            if ($expires) {
                $expiresTime = strtotime($expires);
                if ($expiresTime !== false) {
                    $expires = gmdate('Y-m-d H:i:s', $expiresTime + 8 * 3600); // GMT+8
                } else {
                    $expires = '';
                }
            }
            foreach ($cookieAttributes[1] as $index => $key) {
                $value = $cookieAttributes[2][$index];
                if (!in_array(strtolower($key), ['path', 'expires', 'samesite', 'secure', 'httponly'])) {
                    $arr[] = [
                        'key'     => $key,
                        'value'   => $value,
                        'path'    => $path,
                        'expires' => $expires,
                    ];
                }
            }
        }
        return $arr ?? [];
    }

    /**
     * curl_get_info array
     * @param string|int|null $key
     * @param mixed           $def
     * @return mixed
     */
    public function info(string|int|null $key = null, mixed $def = ''): mixed {
        if (isset($key)) {
            return $this->info[$key] ?? $def;
        }
        return $this->info;
    }
}