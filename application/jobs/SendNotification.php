<?php

namespace app\jobs;

use app\notifications\BaseNotification;
use app\models\User;
use Yii;
use yii\base\BaseObject;
use yii\queue\Queue;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\jobs
 */
class SendNotification extends BaseObject implements \yii\queue\JobInterface
{
    /**
     * @var BaseNotification
     */
    public $notification;
    /**
     * @var int
     */
    public $receiverId;

    /**
     * @param Queue $queue
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        $receiver = User::findOne(['id' => $this->receiverId]);
        if ($receiver !== null) {
            if (isset($receiver->profile->language_id)) {
                Yii::$app->language = $receiver->profile->language_id;
            }
            Yii::$app->notificationManager->send($this->notification, $receiver);
        }
    }
}
