<?php
/**
 * @author Chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/31
 * Time: 20:16
 */

namespace Chenxb\Sms\Contracts;

use Chenxb\Sms\Supports\Config;

/**
 * Interface GatewayInterface.
 */
interface GatewayInterface
{

    CONST API_TIMEOUT = 10.0;

    /**
     * Get gateway name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Send a short message.
     *
     * @param PhoneNumberInterface $to
     * @param MessageInterface $message
     * @param Config $config
     *
     * @return array
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config): array;
}
