<?php

namespace app\models\query;

use app\models\Conversation;
use yii\db\Expression;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models\query
 */
class ConversationQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        parent::init();
        $this
            ->alias('c')
            ->select([
                'c.*',
                'last_message_id' => new Expression('MAX([[c.id]])'),
            ])
            ->andWhere(['or',
                ['to_user_id' => new Expression(':userId'), 'is_deleted_by_receiver' => false],
                ['from_user_id' => new Expression(':userId'), 'is_deleted_by_sender' => false],
            ])
            ->groupBy('contact_id');
    }

    /**
     * @return $this
     */
    public function withUserInfo()
    {
        return $this->joinWith(['sender', 'senderProfile', 'receiver', 'receiverProfile']);
    }

    /**
     * @return $this
     */
    public function withContact()
    {
        return $this->addSelect([
            'contact_id' => new Expression('IF([[from_user_id]] = :userId, [[to_user_id]], [[from_user_id]])')
        ]);
    }

    /**
     * @param int $userId
     * @return $this
     * @since 2.0
     */
    public function forUser($userId)
    {
        return $this->addParams(['userId' => $userId]);
    }
}
