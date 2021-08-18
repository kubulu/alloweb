<?php

namespace app\notifications;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\notifications
 */
class ProfileLikeCategory extends BaseNotificationCategory
{
    /**
     * @var string
     */
    public $id = 'likes';
    /**
     * @var int
     */
    public $sortOrder = 100;

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('app', 'Likes');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return Yii::t('app', 'Receive Notifications for profile likes');
    }
}
