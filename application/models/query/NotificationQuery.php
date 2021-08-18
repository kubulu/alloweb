<?php

namespace app\models\query;

use app\models\Notification;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class NotificationQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Notification[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Notification|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return NotificationQuery
     */
    public function onlyNew()
    {
        return $this->andWhere(['notification.is_viewed' => 0]);
    }

    /**
     * @param $userId
     * @return $this
     */
    public function whereUserId($userId)
    {
        return $this->andWhere([
            'notification.user_id' => (int) $userId,
        ]);
    }

    /**
     * @param $senderId
     * @return $this
     */
    public function bySenderId($senderId)
    {
        return $this->andWhere([
            'notification.sender_user_id' => (int) $senderId,
        ]);
    }
}
