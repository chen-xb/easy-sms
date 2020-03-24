<?php
/**
 * @author Chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2020/01/02
 * Time: 20:03
 */

namespace Chenxb\Sms\Strategies;

use Chenxb\Sms\Contracts\StrategyInterface;

/**
 * Class OrderStrategy
 * @package Chenxb\Sms\Strategies
 */
class OrderStrategy implements StrategyInterface
{

    /**
     * sort send gateway.
     *
     * @param array $gateways
     * @return array
     */
    public function apply(array $gateways): array
    {
        return $gateways;
    }
}
