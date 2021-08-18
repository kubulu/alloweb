<?php

namespace app\payments;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
class GiftTransaction extends TransactionInfo
{
    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE_GIFT;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('app', 'Gift');
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return null;
    }
}
