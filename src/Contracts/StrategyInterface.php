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
 * Interface StrategyInterface.
 */
interface StrategyInterface
{
    /**
     * apply the strategy and return result.
     *
     * @param array $gateways
     *
     * @return array
     */
    public function apply(array $gateways): array;
}
