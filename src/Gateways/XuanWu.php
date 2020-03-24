<?php
/**
 * @author Chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/31
 * Time: 20:16
 */

namespace Chenxb\Sms\Gateways;

use Chenxb\Sms\Contracts\GatewayInterface;
use Chenxb\Sms\Contracts\MessageInterface;
use Chenxb\Sms\Contracts\PhoneNumberInterface;
use Chenxb\Sms\Exceptions\GatewayException;
use Chenxb\Sms\Supports\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Xuanwu
 * @package Chenxb\Sms\Gateways
 */
class XuanWu implements GatewayInterface
{

    CONST API_URL = 'http://211.147.239.62:9051';

    CONST API_ACTION = '/api/v1.0.0/message/mass/send';

    /**
     * return gateway name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'xuanwu';
    }

    /**
     * send message.
     *
     * @param PhoneNumberInterface $to
     * @param MessageInterface $message
     * @param Config $config
     * @return array code|msg|uuid
     * @throws GatewayException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config): array
    {
        // 组织请求内容
        $body = json_encode([
            'batchName' => $config['batch_name'],
            'items' => [
                ['to' => $to->getNumber()]
            ],
            'content' => $message->getContent(),
            'msgType' => 'sms',
        ], JSON_UNESCAPED_UNICODE);

        try {
            // 创建http请求对象
            $client = new Client([
                'base_uri' => static::API_URL,
                'timeout' => $config['timeout'] ?? GatewayInterface::API_TIMEOUT,
            ]);

            // 执行请求
            $response = $client->post(static::API_ACTION, [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Content-Length' => strlen($body),
                    'Accept' => 'application/json',
                    'Authorization' => base64_encode("{$config['account']}:" . md5($config['password'])),
                ],
                'body' => $body
            ]);

            // 结果解析
            $body = $response->getBody();
            $json = \json_decode($body, true);

            // 结果分析
            if (!$json) {
                throw new GatewayException("Xuanwu Gateway Response Invalid: {$body}");
            }
            if ('0' != $json['code']) {
                throw new GatewayException("Xuanwu Gateway Response Error: {$json['msg']}");
            }

            return $json;

        } catch (GuzzleException $e) {
            throw new GatewayException("Xuanwu Gateway Http Request Error: {$e->getMessage()}");
        }
    }
}