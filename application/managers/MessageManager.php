<?php

namespace app\managers;

use app\events\MessageEvent;
use app\models\Conversation;
use app\models\Message;
use app\models\query\ConversationQuery;
use app\models\query\MessageQuery;
use Yii;
use yii\base\Component;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\managerss
 */
class MessageManager extends Component
{
    const EVENT_BEFORE_MESSAGE_CREATE = 'onBeforeMessageCreate';
    const EVENT_AFTER_MESSAGE_CREATE = 'onAfterMessageCreate';

    /**
     * @param $userId
     * @param string $searchQuery
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getConversations($userId, $searchQuery = null)
    {
        $query = $this->getConversationsQuery($userId)
            ->with('lastMessage')
            ->withContact()
            ->withUserInfo()
            ->indexBy('contact_id')
            ->orderBy([
                'last_message_id' => SORT_DESC,
            ]);

        $query->limit(50);
        if ($query !== null) {
            $query->andFilterWhere(['or',
                ['like', 'senderProfile.name', $searchQuery],
                ['like', 'receiverProfile.name', $searchQuery],
            ]);
        }

        $conversations = $query->all();
        $premium = [];
        $free = [];
        foreach ($conversations as $contactId => $conversation) {
            $fields = $conversation->fields();
            $contact = call_user_func($fields['contact'], $conversation);
            if ($contact['premium'] === true) {
                $premium[$contactId] = $conversation;
            } else {
                $free[$contactId] = $conversation;
            }
        }

        return array_merge($premium, $free);
    }

    /**
     * @param $fromUserId
     * @param $toUserId
     * @return Message[]|array
     */
    public function getMessages($fromUserId, $toUserId)
    {
        $query = Message::find()
            ->between($fromUserId, $toUserId)
            ->withUserData($toUserId)
            ->withType($toUserId)
            ->limit(100)
            ->indexBy('id');

        return (array) $query->all();
    }

    /**
     * @param $targetUserId
     * @param $ids
     * @return Message[]|array
     */
    public function getMessagesForUser($targetUserId, $ids)
    {
        $query = Message::find()
            ->whereTargetUser($targetUserId)
            ->andWhere(['in', 'id', $ids]);

        return $query->all();
    }

    /**
     * @param $userId
     * @param $contactId
     * @param $limit
     * @param bool $history
     * @param null $key
     * @return ActiveDataProvider
     */
    public function getMessagesProvider($userId, $contactId, $limit, $history = true, $key = null)
    {
        $query = $this->getMessagesQuery($userId, $contactId);

        if (null !== $key) {
            $query->andWhere([$history ? '<' : '>', 'id', $key]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit
            ]
        ]);
    }

    /**
     * @param $userId
     * @param bool $history
     * @param null $key
     * @return ActiveDataProvider
     */
    public function getConversationsProvider($userId, $history = true, $key = null)
    {
        $query = Conversation::find()->forUser($userId);
        if (null !== $key) {
            $query->andHaving([$history ? '<' : '>', 'last_message_id', $key]);
        }

        $query->indexBy('last_message_id');

        return new ActiveDataProvider([
            'query' => $query,
            'key' => 'last_message_id',
        ]);
    }

    /**
     * @param $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getNewMessagesCounts($userId)
    {
        return Message::find()
            ->onlyNew()
            ->select([
                'from_user_id as contact_id',
                'sum(is_new) AS new_messages_count',
            ])
            ->andWhere(['to_user_id' => $userId, 'is_deleted_by_receiver' => 0])
            ->groupBy('from_user_id')
            ->indexBy('contact_id')
            ->asArray()
            ->all();
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getNewMessagesCount($userId)
    {
        return (int) Message::find()
            ->onlyNew()
            ->addSelect([
                'from_user_id as contact_id',
                'sum(is_new) AS `new_messages_count`',
            ])
            ->andWhere(['to_user_id' => $userId, 'is_deleted_by_receiver' => 0])
            ->groupBy('from_user_id')
            ->sum('is_new');
    }

    /**
     * @param $fromId
     * @param $contactId
     * @param $text
     * @return Message
     */
    public function createMessage($fromId, $contactId, $text)
    {
        $message = new Message(['scenario' => 'create']);
        $message->from_user_id = $fromId;
        $message->to_user_id = $contactId;
        $message->text = $text;

        $event = new MessageEvent;
        $event->message = $message;
        $this->trigger(self::EVENT_BEFORE_MESSAGE_CREATE, $event);

        if ($event->isValid) {
            $message->save();
            $this->trigger(self::EVENT_AFTER_MESSAGE_CREATE, $event);
        }

        return $message;
    }

    /**
     * @param $userId
     * @param $ids
     * @return int
     */
    public function deleteMessages($userId, $ids)
    {
        $messages = $this->getMessagesForUser($userId, $ids);
        $count = 0;

        foreach ($messages as $message) {
            if ($message->from_user_id == Yii::$app->user->id) {
                $message->is_deleted_by_sender = 1;
            } else {
                $message->is_deleted_by_receiver = 1;
            }
            if ($message->save()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param $fromUserId
     * @param $toUserId
     * @return MessageQuery
     */
    protected function getMessagesQuery($fromUserId, $toUserId)
    {
        return Message::find()
            ->orderBy(['id' => SORT_DESC])
            ->between($fromUserId, $toUserId);
    }

    /**
     * @param $userId
     * @return ConversationQuery
     */
    protected function getConversationsQuery($userId)
    {
        return Conversation::find()
            ->forUser($userId);
    }

    /**
     * @param $userId
     * @param $contactId
     * @return array the number of rows updated
     */
    public function deleteConversation($userId, $contactId)
    {
        $count = Conversation::updateAll([
            'is_deleted_by_sender' => new Expression('IF([[from_user_id]] = :userId, TRUE, is_deleted_by_sender)'),
            'is_deleted_by_receiver' => new Expression('IF([[to_user_id]] = :userId, TRUE, is_deleted_by_receiver)')
        ], ['or',
            ['to_user_id' => new Expression(':userId'), 'from_user_id' => $contactId, 'is_deleted_by_receiver' => false],
            ['from_user_id' => new Expression(':userId'), 'to_user_id' => $contactId, 'is_deleted_by_sender' => false],
        ], [
            'userId' => $userId
        ]);

        return compact('count');
    }


    /**
     * @param $userId
     * @param $contactId
     * @return array the number of rows updated
     */
    public function readConversation($userId, $contactId)
    {
        $count = Conversation::updateAll(['is_new' => false], [
            'to_user_id' => $userId,
            'from_user_id' => $contactId,
            'is_new' => true
        ]);

        return compact('count');
    }

    /**
     * @param $userId
     * @param $contactId
     * @return array
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function unreadConversation($userId, $contactId)
    {
        /** @var Message $message */
        $message = Message::find()
            ->where(['from_user_id' => $contactId, 'to_user_id' => $userId, 'is_deleted_by_receiver' => false])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();

        $count = 0;
        if ($message) {
            $message->is_new = 1;
            $count = intval($message->update());
        }

        return compact('count');
    }
}
