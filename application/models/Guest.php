<?php

namespace app\models;

use app\models\query\GuestQuery;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\models
 * @property int $id
 * @property int $from_user_id
 * @property int $visited_user_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $fromUser
 * @property User $visitedUser
 * @property bool $isVisitOld
 *
 * @method touch($attribute)
 */
class Guest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @return GuestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GuestQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%guest}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_user_id', 'visited_user_id'], 'required'],
            [['from_user_id', 'visited_user_id', 'created_at', 'updated_at'], 'integer'],
            [['from_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['from_user_id' => 'id']
            ],
            [['visited_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['visited_user_id' => 'id']
            ],
        ];
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_user_id' => Yii::t('app', 'From User'),
            'visited_user_id' => Yii::t('app', 'Visited User'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVisitedUser()
    {
        return $this->hasOne(User::class, ['id' => 'visited_user_id']);
    }

    /**
     * @return bool
     */
    public function getIsVisitOld()
    {
        return time() - $this->updated_at >= Yii::$app->params['guestVisitThreshold'];
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getLastUpdate()
    {
        return Yii::$app->formatter->asDatetime($this->updated_at, 'medium');
    }
}
