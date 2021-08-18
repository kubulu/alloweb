<?php

namespace app\jobs;

use app\notifications\BaseNotification;
use Yii;
use yii\base\BaseObject;
use yii\db\ActiveQuery;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\jobs
 */
class SendBulkNotification extends BaseObject implements \yii\queue\JobInterface
{
    /**
     * @var BaseNotification[]
     */
    public $notification;
    /**
     * @var ActiveQuery
     */
    public $query;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        Yii::$app->notificationManager->sendBulk($this->notification, $this->query);
    }
}
