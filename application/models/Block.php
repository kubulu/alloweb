<?php

namespace app\models;

use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 *
 * @property int $id
 * @property int $from_user_id
 * @property int $blocked_user_id
 * @property int $created_at
 *
 * @property User $blockedUser
 * @property User $fromUser
 */
class Block extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%block}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_user_id', 'blocked_user_id'], 'required'],
            [['from_user_id', 'blocked_user_id', 'created_at'], 'integer'],
            [['from_user_id', 'blocked_user_id'], 'unique', 'targetAttribute' => ['from_user_id', 'blocked_user_id']],
            [['from_user_id'], 'compare', 'compareAttribute' => 'blocked_user_id', 'operator' => '!='],
            [['blocked_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['blocked_user_id' => 'id']
            ],
            [['from_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['from_user_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_user_id' => Yii::t('app', 'From User'),
            'reported_user_id' => Yii::t('app', 'Blocked User'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlockedUser()
    {
        return $this->hasOne(User::class, ['id' => 'blocked_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }
}
