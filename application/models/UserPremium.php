<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 * @property int $user_id
 * @property int $premium_until
 * @property boolean $incognito_active
 * @property boolean $show_online_status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property-read bool $isPremium
 */
class UserPremium extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_premium';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['premium_until', 'created_at', 'updated_at'], 'integer'],
            [['incognito_active', 'show_online_status'], 'boolean'],
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
            'premium_until' => Yii::t('app', 'Premium Until'),
            'incognito_active' => Yii::t('app', 'Incognito active'),
            'show_online_status' => Yii::t('app', 'Show online status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
     * @return bool
     */
    public function getIsPremium()
    {
        return $this->premium_until > time();
    }

    /**
     * @param $userId
     * @param $daysCount
     * @return UserPremium|null|static
     */
    public static function activatePremium($userId, $daysCount)
    {
        $model = self::findOne(['user_id' => $userId]);
        if ($model == null) {
            $model = new self;
            $model->user_id = $userId;
        }
        $model->premium_until = time() + $daysCount * 86400;
        $model->save();

        return $model;
    }
}
