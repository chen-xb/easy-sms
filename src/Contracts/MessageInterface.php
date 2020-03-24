<?php
/**
 * @author Chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/31
 * Time: 20:16
 */

namespace Chenxb\Sms\Contracts;

/**
 * Interface MessageInterface.
 */
interface MessageInterface
{

    const TEXT_MESSAGE = 'text';

    const VOICE_MESSAGE = 'voice';

    /**
     * return the message type.
     *
     * @return string
     */
    public function getMessageType(): string;

    /**
     * return message content.
     *
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * return the template id of message.
     *
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * return the template data of message.
     *
     * @return array
     */
    public function getData(): array;
}
