<?php

namespace app\payments;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\payments
 */
class SpotlightTransaction extends TransactionInfo
{
    /**
     * @var integer
     */
    public $photoId;
    /**
     * @var string
     */
    public $message;

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE_SPOTLIGHT;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('app', 'Spotlight');
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return null;
    }
}
