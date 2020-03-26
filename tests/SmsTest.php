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
use Chenxb\Sms\Gateways\KuaiYiTong;
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
     * 测试玄武短信接口
     *
     * @throws \Chenxb\Sms\Exceptions\NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testXuanWu()
    {
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
            ],
        ];

        $result = Sms::make($config)->send(getenv('TEST_PHONE_NUMBER'), '验证码为:123456, 验证码五分钟有效');
        $this->assertIsArray($result);
    }

    /**
     * 测试阿里云短信接口
     *
     * @throws \Chenxb\Sms\Exceptions\NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testAliyun()
    {
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

        $result = Sms::make($config)->send(getenv('TEST_PHONE_NUMBER'), [
            'template' => 'SMS_186618722',
            'data' => ['code' => 654321]
        ]);

        $this->assertIsArray($result);
    }

    /**
     * 测试阿里云短信接口
     *
     * @throws \Chenxb\Sms\Exceptions\NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testAliyunFail()
    {
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
                    'sign_name' => '青瓷游戏',
                ],
            ],
        ];

        $this->expectException(NoGatewayAvailableException::class);

        Sms::make($config)->send(getenv('TEST_PHONE_NUMBER'), [
            'template' => 'SMS_186618722',
            'data' => ['code' => 654321]
        ]);
    }

    /**
     * 测试快易通接口
     *
     * @throws \Chenxb\Sms\Exceptions\NoGatewayAvailableException
     * @throws \Chenxb\Sms\Exceptions\RuntimeException
     */
    public function testKuaiYiTong()
    {
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 30.0,
            // 错误日志记录
            'error_log' => '/tmp/sms.log',
            // 执行策略
            'strategy' => OrderStrategy::class,
            // 可用的网关配置
            'gateways' => [
                // 快易通
                'kuai_yi_tong' => [
                    'appkey' => getenv('KUAI_YI_TONG_APPKEY'),
                    'appsecret' => getenv('KUAI_YI_TONG_APPSECRET')
                ],
            ],
        ];

        $result = Sms::make($config)->send(getenv('TEST_PHONE_NUMBER'), '【青瓷游戏】验证码为:456789, 验证码五分钟有效');
        $this->assertIsArray($result);
    }

    /**
     * 测试只要一个短信接口成功就结束
     */
    public function testOnlyOneSend()
    {
        $result = $this->sms->send(getenv('TEST_PHONE_NUMBER'), [
            'content' => '游戏预约成功，验证码为:963852, 验证码五分钟有效。',
            'template' => 'SMS_172007523',
            'data' => ['code' => 963852]
        ]);

        $this->assertIsArray($result);
    }
}