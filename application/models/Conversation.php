<?php

namespace app\models;

use app\models\query\ConversationQuery;
use yii\db\ActiveQuery;
use yii\helpers\StringHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $user_id
 * @property int $contact_id
 * @property int $last_message_id
 *
 * @property User $sender
 * @property Profile $senderProfile
 * @property User $receiver
 * @property Profile $receiverProfile
 */
class Conversation extends \yii\db\ActiveRecord
{
    public $newMessagesCounts;

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastMessage()
    {
        return $this->hasOne(Message::class, ['id' => 'last_message_id'])->orderBy('created_at desc');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewMessages()
    {
        return $this->hasMany(Message::class, ['from_user_id' => 'contact_id', 'to_user_id' => 'user_id'])
            ->andOnCondition(['is_new' => true]);
    }

    /**
     * @return ConversationQuery
     */
    public static function find()
    {
        return new ConversationQuery(get_called_class());
    }

    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return Message::tableName();
    }

    /**
     * @return ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id'])->alias('sender');
    }

    /**
     * @return ActiveQuery
     */
    public function getSenderProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'from_user_id'])->alias('senderProfile');
    }

    /**
     * @return ActiveQuery
     */
    public function getReceiver()
    {
        return $this->hasOne(User::class, ['id' => 'to_user_id'])->alias('receiver');
    }

    /**
     * @return ActiveQuery
     */
    public function getReceiverProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'to_user_id'])->alias('receiverProfile');
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function beforeDelete()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'user_id',
            'user_online',
            'created_at',
            'contact_id',
            'last_message_id',
            'new_messages_count',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function populateRecord($record, $row)
    {
        foreach (['id' ,'user_id', 'contact_id'] as $name) {
            if (isset($row[$name])) {
                $row[$name] = intval($row[$name]);
            }
        }
        parent::populateRecord($record, $row);
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return [
            'id',
            'last_message_id',
            'last_message' => function ($model) {
                return [
                    'text' => StringHelper::truncate($model['lastMessage']['text'], 20),
                    'date' => $model['lastMessage']['created_at'],
                    'fromUserId' => $model['lastMessage']['from_user_id']
                ];
            },
            'contact' => function(Conversation $model) {
                if ($model->contact_id == $model->from_user_id) {
                    $user = $model->sender;
                    $profile = $model->senderProfile;
                } else {
                    $user = $model->receiver;
                    $profile = $model->receiverProfile;
                }
                return [
                    'id' => $user->id,
                    'avatar' => $profile !== null ? $profile->getAvatarUrl(48, 48) : null,
                    'full_name' => $profile !== null ? $profile->getDisplayName() : $user->username,
                    'online' => $user->isOnline,
                    'premium' => (bool) $user->isPremium,
                    'verified' => (bool) $user->profile->is_verified,
                ];
            },
            'created_at',
        ];
    }
}
