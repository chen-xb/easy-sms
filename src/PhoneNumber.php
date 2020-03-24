<?php
/**
 * @author Chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/25
 * Time: 20:03
 */

namespace Chenxb\Sms;

use Chenxb\Sms\Contracts\PhoneNumberInterface;

/**
 * Class PhoneNumber
 * @package Chenxb\Sms
 */
class PhoneNumber implements PhoneNumberInterface
{

    /**
     * @var string
     */
    protected $number;

    /**
     * @var int
     */
    protected $IDDCode;

    /**
     * PhoneNumberInterface constructor.
     *
     * @param string $numberWithoutIDDCode
     * @param string $IDDCode
     */
    public function __construct(string $numberWithoutIDDCode, $IDDCode = null)
    {
        $this->number = $numberWithoutIDDCode;
        $this->IDDCode = $IDDCode ? intval(ltrim($IDDCode, '+0')) : null;
    }

    /**
     * 86.
     *
     * @return int
     */
    public function getIDDCode(): ?int
    {
        return $this->IDDCode;
    }

    /**
     * 18888888888.
     *
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * +8618888888888.
     *
     * @return string
     */
    public function getUniversalNumber(): string
    {
        return $this->getPrefixedIDDCode('+') . $this->number;
    }

    /**
     * 008618888888888.
     *
     * @return string
     */
    public function getZeroPrefixedNumber(): string
    {
        return $this->getPrefixedIDDCode('00') . $this->number;
    }

    /**
     * @param string $prefix
     *
     * @return null|string
     */
    public function getPrefixedIDDCode($prefix): ?string
    {
        return $this->IDDCode ? $prefix . $this->IDDCode : null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUniversalNumber();
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @see  http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->getUniversalNumber();
    }
}
