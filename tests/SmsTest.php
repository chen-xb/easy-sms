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

class SmsTest extends TestCase
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
                ],
                // 快易通
                'kuai_yi_tong' => [
                    'appkey' => getenv('KUAI_YI_TONG_APPKEY'),
                    'appsecret' => getenv('KUAI_YI_TONG_APPSECRET')
                ],
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
     * 测试只要一个短信商成功即可
     *
     * @throws NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testSuccess()
    {
        $result = $this->sms->send(
            getenv('TEST_PHONE_NUMBER'),
            '验证码为：111111。验证码5分钟有效，请勿泄漏给他人。'
        );

        $this->assertIsArray($result);
    }

    /**
     * 测试所有的短信商都失败
     *
     * @throws NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testError()
    {
        $this->expectException(NoGatewayAvailableException::class);

        $this->sms->send(
            '',
            '验证码为：111111。验证码5分钟有效，请勿泄漏给他人。'
        );
    }
}