<?php

namespace app\payments;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
class PremiumTransaction extends TransactionInfo
{
    /**
     * @var string
     */
    public $premiumAt;
    /**
     * @var string
     */
    public $premiumUntil;

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE_PREMIUM;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('app', 'Premium account');
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return null;
    }
}
