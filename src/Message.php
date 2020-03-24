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

/**
 * Class Message
 * @package Chenxb\Sms
 */
class Message implements MessageInterface
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Message constructor.
     *
     * @param array $attributes
     * @param string $type
     */
    public function __construct(array $attributes = [], $type = MessageInterface::TEXT_MESSAGE)
    {
        $this->type = $type;

        foreach ($attributes as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * return the message type.
     *
     * @return string
     */
    public function getMessageType(): string
    {
        return $this->type;
    }

    /**
     * return message content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * return the template id of message.
     *
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * return message data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * return message property.
     *
     * @param $property
     * @return string|null
     */
    public function __get($property): ?string
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return null;
    }
}
