<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 * @property int $user_id
 * @property int $boosted_at
 * @property int $boosted_until
 *
 * @property User $user
 */
class UserBoost extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_boost}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
                'updatedAtAttribute' => 'boosted_at',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boosted_at', 'boosted_until'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User'),
            'boosted_at' => Yii::t('app', 'Boosted At'),
            'boosted_until' => Yii::t('app', 'Boost Until'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @param $userId
     * @param $daysCount
     * @return UserBoost|null|static
     */
    public static function boostUser($userId, $daysCount)
    {
        $model = self::findOne(['user_id' => $userId]);
        if ($model) {
            $model->touch('boosted_at');
        } else {
            $model = new self;
            $model->user_id = $userId;
        }
        $model->boosted_until = time() + $daysCount * 86400;
        $model->save();

        return $model;
    }
}
