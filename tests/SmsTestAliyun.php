<?php
/**
 * @author chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/25
 * Time: 20:44
 */

namespace Chenxb\Sms\Tests;

use Chenxb\Sms\Exceptions\NoGatewayAvailableException;
use Chenxb\Sms\Sms;
use Chenxb\Sms\Strategies\OrderStrategy;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class SmsTestAliyun extends TestCase
{

    /**
     * @var Sms
     */
    protected $sms;

    /**
     * 初始化
     */
    public function setUp()
    {
        $dotEnv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotEnv->load();

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
                'aliyun' => [
                    'access_key_id' => getenv('ALIYUN_ACCESS_KEY_ID'),
                    'access_key_secret' => getenv('ALIYUN_ACCESS_KEY_ID_SECRET'),
                    'sign_name' => getenv('ALIYUN_SIGN_NAME'),
                ],
            ],
        ];

        $this->sms = Sms::make($config);
    }

    /**
     * 正常使用阿里云
     *
     * @throws \Chenxb\Sms\Exceptions\NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testSuccess()
    {
        $result = $this->sms->send(getenv('TEST_PHONE_NUMBER'), [
            'template' => 'SMS_186618722',
            'data' => ['code' => 222222]
        ]);

        $this->assertIsArray($result);
    }

    /**
     * 异常使用阿里云
     *
     * @throws \Chenxb\Sms\Exceptions\NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testError()
    {
        $this->expectException(NoGatewayAvailableException::class);

        $this->sms->send('', [
            'template' => 'SMS_186618722',
            'data' => ['code' => 222222]
        ]);
    }
}