<?php

namespace youdate\widgets;

use app\managers\NotificationManager;
use app\models\Notification;
use Yii;
use yii\base\Widget;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class Notifications extends Widget
{
    /**
     * @var Notification[]
     */
    public $items;
    /**
     * @var
     */
    public $pageSize = 5;

    public function run()
    {
        return $this->render('notifications/header', [
            'items' => $this->getNotifications(),
            'hasNewNotifications' => $this->hasNewNotifications(),
        ]);
    }

    /**
     * @return Notification[]
     * @throws \yii\base\InvalidConfigException
     */
    protected function getNotifications()
    {
        if (isset($this->items)) {
            return $this->items;
        }

        /** @var NotificationManager $manager */
        $manager = Yii::$app->notificationManager;
        $dataProvider = $manager->getNotificationsProvider([
            'userId' => Yii::$app->user->id,
            'onlyNew' => true,
            'pageSize' => 5,
        ]);

        return $dataProvider->getModels();
    }

    /**
     * @return bool
     */
    protected function hasNewNotifications()
    {
        /** @var NotificationManager $manager */
        $manager = Yii::$app->notificationManager;

        return $manager->hasNewNotifications(Yii::$app->user->id);
    }
}
