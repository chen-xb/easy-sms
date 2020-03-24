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
 * Interface PhoneNumberInterface.
 */
interface PhoneNumberInterface extends \JsonSerializable
{

    /**
     * 86.
     *
     * @return int
     */
    public function getIDDCode();

    /**
     * 18888888888.
     *
     * @return int
     */
    public function getNumber();

    /**
     * +8618888888888.
     *
     * @return string
     */
    public function getUniversalNumber();

    /**
     * 008618888888888.
     *
     * @return string
     */
    public function getZeroPrefixedNumber();

    /**
     * @return string
     */
    public function __toString();
}
