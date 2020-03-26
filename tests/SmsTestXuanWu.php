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

class SmsTestXuanWu extends TestCase
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
                // 玄武科技
                'xuan_wu' => [
                    'account' => getenv('XUAN_WU_ACCOUNT'),
                    'password' => getenv('XUAN_WU_PASSWORD'),
                    'batch_name' => getenv('XUAN_WU_BATCH_NAME'),
                ]
            ],
        ];

        $this->sms = Sms::make($config);
    }

    /**
     * 测试玄武短信接口
     *
     * @throws \Chenxb\Sms\Exceptions\NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testSuccess()
    {
        $result = $this->sms->send(
            getenv('TEST_PHONE_NUMBER'),
            '验证码为：444444。验证码五分钟有效，请勿泄漏给他人。'
        );

        $this->assertIsArray($result);
    }

    /**
     * 异常使用玄武科技
     *
     * @throws \Chenxb\Sms\Exceptions\NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testError()
    {
        $this->expectException(NoGatewayAvailableException::class);

        $result = $this->sms->send(
            '',
            '验证码为：444444。验证码五分钟有效，请勿泄漏给他人。'
        );

        $this->assertIsArray($result);
    }
}