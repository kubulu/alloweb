<?php

namespace app\payments;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
class AdminBonusTransaction extends TransactionInfo
{
    /**
     * @var string
     */
    public $notes;

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE_PAYMENT;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->notes;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return null;
    }
}
