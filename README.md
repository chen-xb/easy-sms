# Chenxb/sms
短信接口请求，目前包含的接口
- 阿里云
- 快易通
- 玄武科技

## 安装
```shell
$ composer require chenxb/sms -vvv
```

## 配置
```php
<?php

use Chenxb\Sms\Sms;
use Chenxb\Sms\Strategies\OrderStrategy;
use Chenxb\Sms\Gateways\Aliyun;
use Chenxb\Sms\Gateways\KuaiYiTong;
use Chenxb\Sms\Gateways\XuanWu;

$config = [
    // HTTP 请求的超时时间（秒）
    'timeout' => 30.0,

    // 错误日志记录
    'error_log' => '/tmp/sms.log',

    // 执行策略
    'strategy' => OrderStrategy::class,

    // 可用的网关配置
    'gateways' => [
        // 阿里云
        Aliyun::class => [
            'access_key_id' => 'xxxxxxxxxxx',
            'access_key_secret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'sign_name' => 'xxxxxxx',
        ],
        // 玄武科技
        XuanWu::class => [
            'account' => 'xxxxxxxxxx',
            'password' => 'xxxxxxxxxxx',
            'batch_name' => 'xxxxxxxxxxx',
        ],
        // 快易通
        KuaiYiTong::class => [
            'appkey' => 'xxxxxxxxxx',
            'appsecret' => 'xxxxxxxxxx'
        ],
    ],
];

$sms = Sms::make($config);
$result = $this->sms->send('13800000001', [
    'content' => '验证码为:123456, 验证码五分钟有效，请勿泄漏给他人。',
    'template' => 'ALIYUN_TEMPLATE_NAME',
    'data' => ['code' => 123456]
]);
```