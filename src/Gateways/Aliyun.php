<?php
/**
 * @author chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/25
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

class Aliyun implements GatewayInterface
{

    const ENDPOINT_URL = 'http://dysmsapi.aliyuncs.com';

    const ENDPOINT_METHOD = 'SendSms';

    const ENDPOINT_VERSION = '2017-05-25';

    const ENDPOINT_FORMAT = 'JSON';

    const ENDPOINT_REGION_ID = 'cn-hangzhou';

    const ENDPOINT_SIGNATURE_METHOD = 'HMAC-SHA1';

    const ENDPOINT_SIGNATURE_VERSION = '1.0';

    /**
     * return gateway name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'aliyun';
    }

    /**
     * send message.
     *
     * @param PhoneNumberInterface $to
     * @param MessageInterface $message
     * @param Config $config
     * @return array
     * @throws GatewayException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config): array
    {
        $params = [
            'RegionId' => self::ENDPOINT_REGION_ID,
            'AccessKeyId' => $config['access_key_id'],
            'Format' => self::ENDPOINT_FORMAT,
            'SignatureMethod' => self::ENDPOINT_SIGNATURE_METHOD,
            'SignatureVersion' => self::ENDPOINT_SIGNATURE_VERSION,
            'SignatureNonce' => uniqid(),
            'Timestamp' => $this->getTimestamp(),
            'Action' => self::ENDPOINT_METHOD,
            'Version' => self::ENDPOINT_VERSION,
            'PhoneNumbers' => !\is_null($to->getIDDCode()) ? strval($to->getZeroPrefixedNumber()) : $to->getNumber(),
            'SignName' => $config['sign_name'],
            'TemplateCode' => $message->getTemplate(),
            'TemplateParam' => json_encode($message->getData(), JSON_FORCE_OBJECT),
        ];
        $params['Signature'] = $this->generateSign($config->get('access_key_secret'), $params);

        try {
            // 创建http请求对象
            $client = new Client([
                'base_uri' => static::ENDPOINT_URL,
                'timeout' => $config['timeout'] ?? GatewayInterface::API_TIMEOUT,
            ]);

            // 执行请求
            $response = $client->get('/', ['query' => $params]);

            // 结果解析
            $body = $response->getBody();
            $json = \json_decode($body, true);

            // 结果分析
            if (!$json) {
                throw new GatewayException("Aliyun Gateway Response Invalid: {$body}");
            }
            if ('OK' != $json['Code']) {
                throw new GatewayException("Aliyun Gateway Response Error: {$json['Message']}");
            }

            return $json;

        } catch (GuzzleException $e) {
            throw new GatewayException("Aliyun Gateway Http Request Error: {$e->getMessage()}");
        }
    }

    /**
     * @param string $accessKeySecret
     * @param array $params
     * @return string
     */
    protected function generateSign(string $accessKeySecret, array $params): string
    {
        ksort($params);
        $stringToSign = 'GET&%2F&' . urlencode(http_build_query($params, null, '&', PHP_QUERY_RFC3986));
        return base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
    }

    /**
     * @return false|string
     */
    protected function getTimestamp()
    {
        $timezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
        $timestamp = date('Y-m-d\TH:i:s\Z');
        date_default_timezone_set($timezone);

        return $timestamp;
    }
}