<?php

namespace app\payments;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
class StripeTransaction extends TransactionInfo
{
    /**
     * @var array
     */
    public $stripeData;

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
        return Yii::t('app', 'Payment');
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return 'stripe';
    }
}
