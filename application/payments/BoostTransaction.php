<?php

namespace app\payments;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
class BoostTransaction extends TransactionInfo
{
    /**
     * @var integer
     */
    public $boostedAt;
    /**
     * @var
     */
    public $boostedUntil;

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE_BOOST;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('app', 'Rise up in search');
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return null;
    }
}
