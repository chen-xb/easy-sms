<?php
/**
 * @author Chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/25
 * Time: 20:03
 */

namespace Chenxb\Sms;

use Chenxb\Sms\Contracts\MessageInterface;
use Chenxb\Sms\Contracts\PhoneNumberInterface;
use Chenxb\Sms\Exceptions\NoGatewayAvailableException;
use Chenxb\Sms\Exceptions\RuntimeException;
use Chenxb\Sms\Supports\Config;

/**
 * Class Messenger
 * @package Chenxb\Sms
 */
class Messenger
{

    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    /**
     * @var Sms
     */
    protected $sms;

    /**
     * Messenger constructor.
     *
     * @param Sms $sms
     */
    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }

    /**
     * 发送短信
     *
     * @param PhoneNumberInterface $to
     * @param MessageInterface $message
     * @return array
     * @throws NoGatewayAvailableException
     * @throws RuntimeException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message): array
    {
        // 所有网关配置
        $gateways = $this->sms->getConfig()->get('gateways', []);
        if (!$gateways) {
            throw new RuntimeException('gateway not configured');
        }

        // 日志对象
        $logger = $this->sms->makeLogger($this->sms->getConfig()->get('error_log'));

        // 网关执行顺序策略
        $gateways = $this->sms->makeStrategy()->apply($gateways);

        // 轮询短信商
        $errors = [];
        foreach ($gateways as $gateway => $config) {
            try {
                $gateway = $this->sms->makeGateway($gateway);
                $result = $gateway->send($to, $message, new Config($config));
                return array_merge(['gateway' => $gateway->getName()], $result);
            } catch (\Throwable $e) {
                $errors[$gateway->getName()] = $e->getMessage();
            }
        }

        throw new NoGatewayAvailableException('all gateways fail, detail use $exception->getError() get ', 0, $errors);
    }
}
