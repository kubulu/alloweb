<?php

namespace app\notifications;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class ProfileViewCategory extends BaseNotificationCategory
{
    /**
     * @var string
     */
    public $id = 'guests';

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('app', 'Guests');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return Yii::t('app', 'Receive Notifications for profile views');
    }
}
