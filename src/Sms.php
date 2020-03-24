<?php
/**
 * @author Chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2020/01/02
 * Time: 20:03
 */

namespace Chenxb\Sms;

use Chenxb\Sms\Contracts\GatewayInterface;
use Chenxb\Sms\Contracts\MessageInterface;
use Chenxb\Sms\Contracts\PhoneNumberInterface;
use Chenxb\Sms\Contracts\StrategyInterface;
use Chenxb\Sms\Exceptions\RuntimeException;
use Chenxb\Sms\Strategies\OrderStrategy;
use Chenxb\Sms\Supports\Config;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Sms
 * @package Chenxb\Sms
 */
class Sms
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StrategyInterface
     */
    protected $strategy;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $gateways = [];

    /**
     * Sms constructor.
     *
     * @param array $config
     */
    protected function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    /**
     * make instance.
     *
     * @param array $config
     * @return Sms
     */
    public static function make(array $config): Sms
    {
        return new static($config);
    }

    /**
     * get config instance.
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * send message entrance.
     *
     * @param $to
     * @param $message
     * @return array
     * @throws Exceptions\NoGatewayAvailableException
     * @throws Exceptions\RuntimeException
     */
    public function send($to, $message): array
    {
        $to = $this->formatPhoneNumber($to);
        $message = $this->formatMessage($message);

        return $this->makeMessenger()->send($to, $message);
    }

    /**
     * make messenger instance.
     *
     * @return Messenger
     */
    public function makeMessenger(): Messenger
    {
        return (new Messenger($this));
    }

    /**
     * make strategy instance.
     *
     * @return StrategyInterface
     */
    public function makeStrategy(): StrategyInterface
    {
        if (!$this->strategy instanceof StrategyInterface) {
            $class = $this->getConfig()->get('strategy', OrderStrategy::class);
            $this->strategy = new $class();
        }

        return $this->strategy;
    }

    /**
     * make logger instance.
     *
     * @param string $path
     * @return Logger
     * @throws \Exception
     */
    public function makeLogger(string $path): Logger
    {
        if (!$this->logger instanceof Logger) {
            $this->logger = new Logger('chenxb-sms');
            $this->logger->pushHandler(new StreamHandler($path, Logger::DEBUG));
        }

        return $this->logger;
    }

    /**
     * make gateway instance.
     *
     * @param string $class
     * @return GatewayInterface
     * @throws RuntimeException
     */
    public function makeGateway(string $class): GatewayInterface
    {
        try {
            $class = ucfirst($class);

            if (empty($this->gateways[$class])) {
                $reflectionClass = new \ReflectionClass($class);
                $this->gateways[$class] = $reflectionClass->newInstance();
            }

            return $this->gateways[$class];

        } catch (\ReflectionException $e) {
            throw new RuntimeException("{$class} Not Exists");
        }
    }

    /**
     * format phone number instance.
     *
     * @param $to
     * @return PhoneNumberInterface
     */
    protected function formatPhoneNumber($to): PhoneNumberInterface
    {
        if ($to instanceof PhoneNumberInterface) {
            return $to;
        }

        return new PhoneNumber(trim($to));
    }

    /**
     * format message instance.
     *
     * @param $message
     * @return MessageInterface
     */
    protected function formatMessage($message): MessageInterface
    {
        if (!($message instanceof MessageInterface)) {
            if (!is_array($message)) {
                $message = [
                    'content' => strval($message),
                    'template' => strval($message),
                ];
            }

            $message = new Message($message);
        }

        return $message;
    }
}