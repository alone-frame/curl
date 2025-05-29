# 设置全局代理IP

```php
<?php
\AloneFrame\curl\CurlRequest::proxy([
    //默认使用名称
    'default' => 'default',
    //代理配置列表
    'config'  => [
        'default' => [
            //ip
            'ip'   => '',
            //端口
            'port' => '',
            //认证信息
            'user' => '',
            //http,socks5
            'type' => '',
            //basic,ntlm
            'auth' => ''
        ]
    ]
]);
```

# 单个请求

```php
<?php
$curl = \AloneFrame\curl\CurlRequest::call([
    //请求url
    'url'         => '',
    //请求路径
    'path'        => '',
    //请求模式(get,post,put,patch,delete,head,connect,options), 支持请求体[post,put,patch,delete]
    'mode'        => 'get',
    //设置头部信息
    'header'      => [],
    //是否ajax提交
    'ajax'        => false,
    //是否json
    'json'        => false,
    //请求体
    'body'        => [],
    //是否自动跳转,默认跳转
    'follow'      => true,
    //上传文件(body要设置array)
    'file'        => '',
    //设置cookie
    'cookie'      => [],
    //设置来路,true=使用当前域名,string自定
    'origin'      => false,
    //设置浏览器信息,true=使用默认浏览器,string自定
    'browser'     => true,
    //设置基本认证信息
    'auth'        => '',
    //设置解码名称
    'encoding'    => '',
    //连接时间,默认10
    'connect'     => 10,
    //超时时间,默认10
    'timeout'     => 10,
    //设置代理ip true=默认代理,string=设置全局代理key,false=关闭,array=设置单独代理
    'proxy'       => false,
    //设置伪装ip
    'req_ip'      => '',
    //伪装ip的key列表
    'req_ip_name' => [],
    //是否检查证书,默认不检查
    'ssl_peer'    => false,
    //是否检查证书公用名,默认不检查
    'ssl_host'    => false,
    //自定义Curl设置
    'curl'        => []
]);

//是否请求成功
dump($curl->status);
//请求信息
dump($curl->request);
//响应时间
dump($curl->time);
//curl_get_info
dump($curl->info);
//响应状态码
dump($curl->code);
//响应头部信息
dump($curl->header);
//响应内容
dump($curl->body);

//调试信息
dump($curl->debug());

//响应内容转换成array
dump($curl->array());

//响应头部信息array,可获取指定key
dump($curl->header());

//curl_get_info 可获取指定key
dump($curl->info());
```

# 批量请求

```php
<?php
$curl = \AloneFrame\curl\CurlRequest::send([
    'demo' => [
        //请求url
        'url'         => '',
        //请求路径
        'path'        => '',
        //请求模式(get,post,put,patch,delete,head,connect,options), 支持请求体[post,put,patch,delete]
        'mode'        => 'get',
        //设置头部信息
        'header'      => [],
        //是否ajax提交
        'ajax'        => false,
        //是否json
        'json'        => false,
        //请求体
        'body'        => [],
        //是否自动跳转,默认跳转
        'follow'      => true,
        //上传文件(body要设置array)
        'file'        => '',
        //设置cookie
        'cookie'      => [],
        //设置来路,true=使用当前域名,string自定
        'origin'      => false,
        //设置浏览器信息,true=使用默认浏览器,string自定
        'browser'     => true,
        //设置基本认证信息
        'auth'        => '',
        //设置解码名称
        'encoding'    => '',
        //连接时间,默认10
        'connect'     => 10,
        //超时时间,默认10
        'timeout'     => 10,
        //设置代理ip true=默认代理,string=设置全局代理key,false=关闭,array=设置单独代理
        'proxy'       => false,
        //设置伪装ip
        'req_ip'      => '',
        //伪装ip的key列表
        'req_ip_name' => [],
        //是否检查证书,默认不检查
        'ssl_peer'    => false,
        //是否检查证书公用名,默认不检查
        'ssl_host'    => false,
        //自定义Curl设置
        'curl'        => []
    ]
]);
//处理全部信息
$curl->handle(function($key, \AloneFrame\curl\process\BodyCall $req) {
    //处理请求后信息
    //是否请求成功
    dump($req->status);
    //请求信息
    dump($req->request);
    //响应时间
    dump($req->time);
    /**
      * 同单请求一样
    */
});

//只获取key为demo的信息
$demo = $curl->exec('demo');
dump($demo->status);
//请求信息
dump($demo->request);
//响应时间
dump($demo->time);
/**
  * 同单请求一样
*/
```